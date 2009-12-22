<?php
/**
 * StatusNet, the distributed open-source microblogging tool
 *
 * Plugin to parse MediaWiki-style freelinks into the Wiki of your choice.
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Plugin
 * @package   StatusNet
 * @author    Eugene Eric Kim <eekim@blueoxen.com>
 * @copyright 2009 Blue Oxen Associates
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://laconi.ca/
 *
 * @see      Event
 */

if (!defined('LACONICA') && !defined('STATUSNET')) {
    exit(1);
}

define('MEDIAWIKIPLUGIN_VERSION', '0.1');

/**
 * Plugin to parse MediaWiki-style freelinks into the Wiki of your choice.
 *
 * Before notices are saved, we parse MediaWiki-style freelinks into URLs.
 *
 * @category  Plugin
 * @package   Laconica
 * @author    Eugene Eric Kim <eekim@blueoxen.com>
 * @copyright 2009 Blue Oxen Associates
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://laconi.ca/
 *
 * @see       Event
 */

class MediaWikiPlugin extends Plugin
{
    public $base_url = 'http://foo/wiki/';

    function onStartNoticeSave($notice)
    {
        // @fixme this may cross link boundaries etc and be funky
        $notice->rendered = preg_replace_callback('/\[\[[^\]]+\]\]/',
                                          array($this, "renderWikiLink"),
                                          $notice->rendered);
        return true;
    }

    function renderWikiLink($match)
    {
        $html = preg_replace('/^\[\[/', '', $match[0]);
        $html = preg_replace('/\]\]$/', '', $html);

        $page = html_entity_decode($html, ENT_COMPAT, 'utf-8');
        $page = str_replace(' ', '_', $page);

        $encoded = urlencode($page);
        $encoded = str_replace('%2F', '/', $encoded);
        $encoded = str_replace('%3A', ':', $encoded);

        $url = $this->base_url . $encoded;
        $encurl = htmlspecialchars($url);
        return "[[<a href=\"$encurl\">$html</a>]]";
    }
}

