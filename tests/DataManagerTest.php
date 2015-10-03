<?php

namespace Jehaby\Viomedia\Tests;


use Jehaby\Viomedia\DataManager;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;


class DataManagerTest extends \PHPUnit_Extensions_Database_TestCase
{

    /**
     * @var DataManager;
     */
    private $dataManager;


    public function setUp()
    {
        parent::setUp();
        $this->dataManager = new DataManager();
    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        $db = new \Jehaby\Viomedia\DB();
        return $this->createDefaultDBConnection($db, 'testdb');
        // TODO: Implement getConnection() method.
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {

        return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(dirname(__FILE__) . '/_files/dataset_1.yml');
        // TODO: Implement getDataSet() method.
    }


    public function testInsertNode()
    {
        $this->assertEquals(8, $this->getConnection()->getRowCount('nodes'), 'Pre-Condition');
        $this->assertTrue($this->dataManager->insertNode('Linus', 10));
        $this->assertEquals(9, $this->getConnection()->getRowCount('nodes'), 'Inserting, failed');
    }


    public function testInsertNodeFolderNotExists()
    {
        $this->assertEquals(8, $this->getConnection()->getRowCount('nodes'), 'Pre-Condition');
        $this->setExpectedException('LogicException', 'There is no folder with such id');
        $this->dataManager->insertNode('Masha', 666);
        $this->assertEquals(8, $this->getConnection()->getRowCount('nodes'), 'Inserting, failed');
    }


    public function testDeleteFolderWithoutChildren()
    {
        $this->assertDataset();
        $this->assertTrue($this->dataManager->deleteFolder(15));
        $this->assertEquals(5, $this->getConnection()->getRowCount('folders'), 'Deletion, failed');
        $this->assertEquals(7, $this->getConnection()->getRowCount('nodes'), 'Deletion, failed');
        $this->assertEquals(3, $this->getConnection()->getRowCount('all_ancestor_folders'), 'Deletion, failed');
    }


    public function testDeleteFolderWithChildren()
    {
        $this->assertDataset();
        $this->assertTrue($this->dataManager->deleteFolder(1));
        $this->assertEquals(2, $this->getConnection()->getRowCount('folders'), 'Deletion, failed');
        $this->assertEquals(2, $this->getConnection()->getRowCount('nodes'), 'Deletion, failed');
        $this->assertEquals(0, $this->getConnection()->getRowCount('all_ancestor_folders'), 'Deletion, failed');
    }


    public function deleteNonexistentFolder()
    {
        $this->assertDataset();
        $this->setExpectedException('LogicException', 'There is no folder with such id');
        $this->dataManager->deleteFolder(3);
        $this->assertDataset('Deletion, failed');
    }


    private function assertDataset($msg = 'Pre-Condition')
    {
        $this->assertEquals(6, $this->getConnection()->getRowCount('folders'), $msg);
        $this->assertEquals(8, $this->getConnection()->getRowCount('nodes'), $msg);
        $this->assertEquals(5, $this->getConnection()->getRowCount('all_ancestor_folders'), $msg);
    }


    public function testCreateFolder()
    {
        $this->assertDataset();
        $this->assertTrue($this->dataManager->createFolder(15));
        $this->assertEquals(7, $this->getConnection()->getRowCount('folders'));
        $this->assertEquals(8, $this->getConnection()->getRowCount('all_ancestor_folders'));
    }


    public function testCreateFolderParentNonexistent()
    {
        $this->assertDataset();
        $this->setExpectedException('LogicException', 'There is no folder with such id');
        $this->dataManager->createFolder(666);
        $this->assertEquals(6, $this->getConnection()->getRowCount('folders'));
        $this->assertEquals(5, $this->getConnection()->getRowCount('all_ancestor_folders'));
    }


    public function createFolderWithParentNull()
    {
        $this->assertDataset();
        $this->assertTrue($this->dataManager->createFolder());
        $this->assertEquals(7, $this->getConnection()->getRowCount('folders'));
    }


    public function testGetFolderIdWithChildrenDontGroup()
    {
        $reflector = new \ReflectionClass('Jehaby\Viomedia\DataManager'); // TODO: try without namespace
        $method = $reflector->getMethod('getFolderIdWithChildren');
        $method->setAccessible(true);

        $this->assertEquals([10, 13, 15],$method->invokeArgs($this->dataManager, [10, true]));
        $this->assertEquals([0, 1, 5, 10, 13, 15],$method->invokeArgs($this->dataManager, [0, true]));
    }


    public function testGetFolderIdWithChildren()
    {
        $reflector = new \ReflectionClass('Jehaby\Viomedia\DataManager'); // TODO: try without namespace
        $method = $reflector->getMethod('getFolderIdWithChildren');
        $method->setAccessible(true);

        $this->assertEquals([
            2 => [10],
            3 => [13, 15]
        ], $method->invokeArgs($this->dataManager, [10]));

        $this->assertEquals([
            0 => [0],
            1 => [1, 5],
            2 => [10],
            3 => [13, 15]
        ], $method->invokeArgs($this->dataManager, [0]));

        $this->setExpectedException('LogicException', 'There is no folder with such id');
        $method->invokeArgs($this->dataManager, [666]);
    }


    public function testGetAllNodes()
    {
        $this->assertDataset();
        $this->assertEquals(
            [
                3 => [        // level
                    13 => [   // folder_id
                            ['id' => 6, 'val' => 'donald'],
                            ['id' => 7, 'val' => 'fred']
                    ]
                ]
            ],
            $this->dataManager->getAllNodes(13)
        );

    }


    public function testGetAllNodesNonexistentFolder()
    {
        $this->assertDataset();
        $this->setExpectedException('LogicException', 'There is no folder with such id');
        $this->dataManager->getAllNodes(666);
    }


    public function testGetAllNodesEmptyFolder()
    {
        $this->assertDataset();
        $this->assertEquals([
            1 => [        // level
                5 => [    // folder_id
                          // empty folder
                ]
            ]
        ], $this->dataManager->getAllNodes(5));
    }

}
