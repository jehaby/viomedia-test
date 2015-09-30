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

        return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(dirname(__FILE__) . '/_files/dataset.yml');
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
        $this->assertFalse($this->dataManager->insertNode('Masha', 666));
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
        $this->assertFalse($this->dataManager->deleteFolder(666));
        $this->assertEquals(6, $this->getConnection()->getRowCount('folders'), 'Deletion, failed');
        $this->assertEquals(8, $this->getConnection()->getRowCount('nodes'), 'Deletion, failed');
        $this->assertEquals(5, $this->getConnection()->getRowCount('all_ancestor_folders'), 'Deletion, failed');
    }


    private function assertDataset()
    {
        $this->assertEquals(6, $this->getConnection()->getRowCount('folders'), 'Pre-Condition');
        $this->assertEquals(8, $this->getConnection()->getRowCount('nodes'), 'Pre-Condition');
        $this->assertEquals(5, $this->getConnection()->getRowCount('all_ancestor_folders'), 'Pre-Condition');
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
        $this->assertFalse($this->dataManager->createFolder(666));

        $this->assertEquals(6, $this->getConnection()->getRowCount('folders'));
        $this->assertEquals(5, $this->getConnection()->getRowCount('all_ancestor_folders'));
    }

    public function createFolderWithParentNull()
    {
        $this->assertDataset();
        $this->assertTrue($this->dataManager->createFolder());
        $this->assertEquals(7, $this->getConnection()->getRowCount('folders'));
    }



}
