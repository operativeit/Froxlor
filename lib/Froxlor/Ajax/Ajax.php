<?php

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, you can also view it online at
 * https://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright  the authors
 * @author     Froxlor team <team@froxlor.org>
 * @license    https://files.froxlor.org/misc/COPYING.txt GPLv2
 */

namespace Froxlor\Ajax;

use Exception;
use Froxlor\Config\ConfigDisplay;
use Froxlor\Config\ConfigParser;
use Froxlor\CurrentUser;
use Froxlor\Database\Database;
use Froxlor\FileDir;
use Froxlor\Froxlor;
use Froxlor\Http\HttpClient;
use Froxlor\Settings;
use Froxlor\UI\Listing;
use Froxlor\UI\Panel\UI;
use Froxlor\UI\Request;
use Froxlor\UI\Response;
use Froxlor\Validate\Validate;

class Ajax
{
	protected string $action;
	protected string $theme;
	protected array $userinfo;

	/**
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->action = $_GET['action'] ?? $_POST['action'] ?? null;
		$this->theme = $_GET['theme'] ?? 'Froxlor';

		UI::sendHeaders();
		UI::sendSslHeaders();
	}

	/**
	 * @throws Exception
	 */
	public function handle()
	{
		$this->userinfo = $this->getValidatedSession();

		switch ($this->action) {
			case 'newsfeed':
				return $this->getNewsfeed();
			case 'updatecheck':
				return $this->getUpdateCheck();
			case 'searchglobal':
				return $this->searchGlobal();
			case 'updatetablelisting':
				return $this->updateTablelisting();
			case 'resettablelisting':
				return $this->resetTablelisting();
			case 'editapikey':
				return $this->editApiKey();
			case 'getConfigDetails':
				return $this->getConfigDetails();
			default:
				return $this->errorResponse('Action not found!');
		}
	}

	/**
	 * @throws Exception
	 */
	private function getValidatedSession(): array
	{
		if (CurrentUser::hasSession() == false) {
			throw new Exception("No valid session");
		}
		return CurrentUser::getData();
	}

	/**
	 * @throws Exception
	 */
	private function getNewsfeed()
	{
		UI::initTwig();

		$feed = "https://inside.froxlor.org/news/";

		// Set custom feed if provided
		if (isset($_GET['role']) && $_GET['role'] == "customer") {
			$custom_feed = Settings::Get("customer.news_feed_url");
			if (!empty(trim($custom_feed))) {
				$feed = $custom_feed;
			}
		}

		// Check for simplexml_load_file
		if (!function_exists("simplexml_load_file")) {
			return $this->errorResponse(
				"Newsfeed not available due to missing php-simplexml extension",
				"Please install the php-simplexml extension in order to view our newsfeed."
			);
		}

		// Check for curl_version
		if (!function_exists('curl_version')) {
			return $this->errorResponse(
				"Newsfeed not available due to missing php-curl extension",
				"Please install the php-curl extension in order to view our newsfeed."
			);
		}

		$output = HttpClient::urlGet($feed);
		$news = simplexml_load_string(trim($output));

		// Handle items
		if ($news) {
			$items = null;

			for ($i = 0; $i < 3; $i++) {
				$item = $news->channel->item[$i];

				$title = (string)$item->title;
				$link = (string)$item->link;
				$date = date("d.m.Y", strtotime($item->pubDate));
				$content = preg_replace("/[\r\n]+/", " ", strip_tags($item->description));
				$content = substr($content, 0, 150) . "...";

				$items .= UI::twig()->render($this->theme . '/user/newsfeeditem.html.twig', [
					'link' => $link,
					'title' => $title,
					'date' => $date,
					'content' => $content
				]);
			}

			return $this->jsonResponse($items);
		} else {
			return $this->errorResponse('No Newsfeeds available at the moment.');
		}
	}

	public function errorResponse($message, int $response_code = 500)
	{
		header("Content-Type: application/json");
		return \Froxlor\Api\Response::jsonErrorResponse($message, $response_code);
	}

	public function jsonResponse($value, int $response_code = 200)
	{
		header("Content-Type: application/json");
		return \Froxlor\Api\Response::jsonResponse($value, $response_code);
	}

	private function getUpdateCheck()
	{
		UI::initTwig();

		try {
			$json_result = \Froxlor\Api\Commands\Froxlor::getLocal($this->userinfo)->checkUpdate();
			$result = json_decode($json_result, true)['data'];
			$result = UI::twig()->render($this->theme . '/misc/version_top.html.twig', $result);
			return $this->jsonResponse($result);
		} catch (Exception $e) {
			// don't display anything if just not allowed due to permissions
			if ($e->getCode() != 403) {
				Response::dynamicError($e->getMessage());
			}
		}
	}

	/**
	 * @todo $userinfo
	 */
	private function searchGlobal()
	{
		$searchtext = Request::get('searchtext');

		$result = [];

		// settings
		$result_settings = [];
		if (isset($this->userinfo['adminsession']) && $this->userinfo['adminsession'] == 1 && $this->userinfo['change_serversettings'] == 1) {
			$result_settings = GlobalSearch::searchSettings($searchtext, $this->userinfo);
		}

		// all searchable entities
		$result_entities = GlobalSearch::searchGlobal($searchtext, $this->userinfo);

		$result = array_merge($result_settings, $result_entities);

		return $this->jsonResponse($result);
	}

	private function updateTablelisting()
	{
		$columns = [];
		foreach (Request::get('columns') as $value) {
			$columns[] = $value;
		}
		Listing::storeColumnListingForUser([Request::get('listing') => $columns]);
		return $this->jsonResponse($columns);
	}

	private function resetTablelisting()
	{
		Listing::deleteColumnListingForUser([Request::get('listing') => []]);
		return $this->jsonResponse([]);
	}

	private function editApiKey()
	{
		$keyid = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$allowed_from = isset($_POST['allowed_from']) ? $_POST['allowed_from'] : "";
		$valid_until = isset($_POST['valid_until']) ? (int)$_POST['valid_until'] : -1;

		// validate allowed_from
		if (!empty($allowed_from)) {
			$ip_list = array_map('trim', explode(",", $allowed_from));
			$_check_list = $ip_list;
			foreach ($_check_list as $idx => $ip) {
				if (Validate::validate_ip2($ip, true, 'invalidip', true, true, true) == false) {
					unset($ip_list[$idx]);
					continue;
				}
				// check for cidr
				if (strpos($ip, '/') !== false) {
					$ipparts = explode("/", $ip);
					// shorten IP
					$ip = inet_ntop(inet_pton($ipparts[0]));
					// re-add cidr
					$ip .= '/' . $ipparts[1];
				} else {
					// shorten IP
					$ip = inet_ntop(inet_pton($ip));
				}
				$ip_list[$idx] = $ip;
			}
			$allowed_from = implode(",", array_unique($ip_list));
		}

		if ($valid_until <= 0 || !is_numeric($valid_until)) {
			$valid_until = -1;
		}

		$upd_stmt = Database::prepare("
			UPDATE `" . TABLE_API_KEYS . "` SET
			`valid_until` = :vu, `allowed_from` = :af
			WHERE `id` = :keyid AND `adminid` = :aid AND `customerid` = :cid
		");
		if ((int)$this->userinfo['adminsession'] == 1) {
			$cid = 0;
		} else {
			$cid = $this->userinfo['customerid'];
		}
		Database::pexecute($upd_stmt, [
			'keyid' => $keyid,
			'af' => $allowed_from,
			'vu' => $valid_until,
			'aid' => $this->userinfo['adminid'],
			'cid' => $cid
		]);
		return $this->jsonResponse(['allowed_from' => $allowed_from, 'valid_until' => $valid_until]);
	}

	/**
	 * return parsed commands/files of configuration templates
	 */
	private function getConfigDetails()
	{
		if (isset($this->userinfo['adminsession']) && $this->userinfo['adminsession'] == 1 && $this->userinfo['change_serversettings'] == 1) {
			$distribution = isset($_POST['distro']) ? $_POST['distro'] : "";
			$section = isset($_POST['section']) ? $_POST['section'] : "";
			$daemon = isset($_POST['daemon']) ? $_POST['daemon'] : "";

			// validate distribution config-xml exists
			$config_dir = FileDir::makeCorrectDir(Froxlor::getInstallDir() . '/lib/configfiles/');
			if (!file_exists($config_dir . "/" . $distribution . ".xml")) {
				return $this->errorResponse("Unknown distribution. The configuration could not be found.");
			}
			// read in all configurations
			$configfiles = new ConfigParser($config_dir . "/" . $distribution . ".xml");
			// get the services
			$services = $configfiles->getServices();
			// validate selected service exists for this distribution
			if (!isset($services[$section])) {
				return $this->errorResponse("Unknown category for selected distribution");
			}
			// get the daemons
			$daemons = $services[$section]->getDaemons();
			// validate selected daemon exists for this section
			if (!isset($daemons[$daemon])) {
				return $this->errorResponse("Unknown service for selected category");
			}
			// finally the config-steps
			$confarr = $daemons[$daemon]->getConfig();
			// get parsed content
			UI::initTwig();
			$content = ConfigDisplay::fromConfigArr($confarr, $configfiles->distributionEditor, $this->theme);

			return $this->jsonResponse([
				'title' => $configfiles->getCompleteDistroName() . '&nbsp;&raquo;&nbsp' . $services[$section]->title . '&nbsp;&raquo;&nbsp' . $daemons[$daemon]->title,
				'content' => $content
			]);
		}
		return $this->errorResponse('Not allowed', 403);
	}
}
