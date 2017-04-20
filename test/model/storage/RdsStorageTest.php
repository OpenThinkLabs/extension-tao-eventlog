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

namespace oat\taoEventLog\test\model\requestLog\rds;

use oat\dtms\DateTime;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoEventLog\model\storage\RdsStorage;
use oat\oatbox\service\ServiceManager;

/**
 * Class RdsStorageTest
 * @package oat\taoEventLog\test\model\requestLog\rds
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class RdsStorageTest extends TaoPhpUnitTestRunner
{

    public function testCount()
    {
        $storage = $this->getService();
        $this->assertEquals(60, $storage->count());

        $result = $storage->search([
            [RdsStorage::EVENT_LOG_USER_ID, 'like', 'test_user_2%']
        ]);
        $this->assertEquals(11, count($result));
    }

    public function testSearch()
    {
        $storage = $this->getService();
        $this->assertEquals(60, count($storage->search()));

        $result = $storage->search([
            [RdsStorage::EVENT_LOG_USER_ID, 'like', 'test_user_2%']
        ], [
            'limit' => 5,
            'offset' => 2,
            'sort'=>RdsStorage::EVENT_LOG_OCCURRED,
            'order'=>'ASC'
        ]);
        $this->assertEquals(5, count($result));
        $this->assertEquals('test_user_21', $result[0][RdsStorage::EVENT_LOG_USER_ID]);


        $result = $storage->search([
            [RdsStorage::EVENT_LOG_USER_ID, '=', 'test_user_1']
        ]);
        $this->assertEquals(1, count($result));
        $this->assertEquals('test_user_1', $result[0][RdsStorage::EVENT_LOG_USER_ID]);


        $result = $storage->search([
            [RdsStorage::EVENT_LOG_OCCURRED, 'between', '2017-04-19 12:00:00', '2017-04-19 12:01:00']
        ], ['sort'=>RdsStorage::EVENT_LOG_OCCURRED, 'order'=>'ASC']);
        $this->assertEquals('2017-04-19 12:00:00', $result[0][RdsStorage::EVENT_LOG_OCCURRED]);
        $this->assertEquals('2017-04-19 12:01:00', $result[1][RdsStorage::EVENT_LOG_OCCURRED]);
        $this->assertEquals(2, count($result));


        $result = $storage->search([
            [RdsStorage::EVENT_LOG_USER_ID, 'like', 'test_user_%'],
            [RdsStorage::EVENT_LOG_EVENT_NAME, 'like', 'test_event_2%'],
        ]);
        $this->assertEquals(11, count($result));
        $this->assertEquals('test_user_29', $result[0][RdsStorage::EVENT_LOG_USER_ID]);//default order is desc
    }

    /**
     * @return RdsStorage
     */
    protected function getService()
    {
        $persistenceManager = $this->getSqlMock('test_eventlog');
        (new \oat\taoEventLog\scripts\install\RegisterRdsStorage)->createTable($persistenceManager->getPersistenceById('test_eventlog'));
        $storage = new RdsStorage([
            RdsStorage::OPTION_PERSISTENCE => 'test_eventlog'
        ]);
        $config = new \common_persistence_KeyValuePersistence([], new \common_persistence_InMemoryKvDriver());
        $config->set(\common_persistence_Manager::SERVICE_ID, $persistenceManager);
        $serviceManager = new ServiceManager($config);
        $storage->setServiceManager($serviceManager);
        $this->loadFixtures($storage);
        return $storage;
    }

    protected function loadFixtures(RdsStorage $storage)
    {
        for ($i = 0; $i < 60; $i++) {
            $storage->log(
                'test_event_' . $i,
                'test_action_' . $i,
                'test_user_' . $i,
                'role_' . (($i%5)+1) . ',role_2' . (($i%5)+2),
                '2017-04-19 12:'.str_pad($i, 2, '0', STR_PAD_LEFT).':00',
                ['id'=>$i]
            );
        }
    }
}