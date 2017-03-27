<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoEventLog\model\requestLog;

use \DateTime;
use GuzzleHttp\Psr7\Request;
use oat\oatbox\user\User;

/**
 * Interface RequestLogStorage
 * @package oat\taoEventLog\model\requestLog
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
interface RequestLogStorage
{
    const USER_ID = 'user_id';
    const USER_ROLES = 'user_role';
    const ACTION = 'action';
    const EVENT_TIME = 'event_time';
    const DETAILS = 'details';

    const SERVICE_ID = 'taoEventLog/RequestLogStorage';

    /**
     * Log request data.
     *
     * @param Request|null $request
     * @param User|null $user
     * @return boolean
     */
    public function log(Request $request = null, User $user = null);

    /**
     * Find user requests.
     *
     * result example:
     * ```
     * [
     *     [...],
     *     [
     *         'user_id' => 'http://sample/first.rdf#i1490617729993174',
     *         'user_role' => 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole,http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiDeliveryProviderManagerRole',
     *         'action' => 'http://yourdomain/tao/Main/index?structure=settings&ext=tao&section=settings_ext_mng',
     *         'event_time' => '1490617792.5479',
     *         'details' => '"{\"login\":\"proctor\"}"', //json encoded additional data
     *     ],
     *     [...],
     * ]
     * ```
     *
     * @param array $filters filters by user id, url, role etc.
     * @param DateTime|null $since
     * @param DateTime|null $until
     * @return array
     */
    public function find(array $filters = [], DateTime $since = null, DateTime $until = null);
}