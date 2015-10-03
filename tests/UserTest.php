<?php

use Jehaby\Viomedia\User;


class UserTest extends \PHPUnit_Extensions_Database_TestCase
{


    public function setUp()
    {
        parent::setUp();
        User::$testing = true;
        $this->user = User::getInstance(42);
        $this->exampleData = include '_files/user_data.php';
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
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(dirname(__FILE__) . '/_files/dataset_2.yml');
    }


    public function testGetAge()
    {
        $this->assertEquals($this->exampleData['age'], $this->user->get('age'));
    }


    public function testGetHomeLocation()
    {
        $this->assertEquals($this->exampleData['home']['location'], $this->user->get('home\\location'));
    }


    public function testGetNonExistentKey()
    {
        $this->setExpectedException('LogicException', 'There is no record with such key');
        $this->user->get('IwannaBecomeGoodProgrammer');
        $this->user->get('age\\SmellsLikeTeenSpirit');
    }

    public function testGetKeyEmptyString()
    {
        $this->assertEquals($this->exampleData, $this->user->get(''));
    }


    public function testSet()
    {
        $this->user->set('new_value', 'Something Very Important');
        $this->assertEquals('Something Very Important', $this->user->get('new_value'));
        $this->assertEquals(
            'Something Very Important',
            unserialize(
                $this->getConnection()->createQueryTable('test', 'SELECT storage FROM users WHERE id=42')
                    ->getRow(0)['storage']
            )['new_value']
        );
    }


    public function testSetLongKey()
    {
        $this->user->set('some\\really\\very\\long\\key', '1');
        $this->assertEquals('1', $this->user->get('some\\really\\very\\long\\key'));
        $this->assertEquals(
            '1',
            unserialize(
                $this->getConnection()->createQueryTable('test', 'SELECT storage FROM users WHERE id=42')
                    ->getRow(0)['storage']
            )['some']['really']['very']['long']['key']
        );
    }


    public function testSetEmptyKey()
    {
        $this->user->set('', 'really empty');
        $this->assertEquals('really empty', $this->user->get(''));
        $this->assertEquals(
            'really empty',
            unserialize(
                $this->getConnection()->createQueryTable('test', 'SELECT storage FROM users WHERE id=42')
                    ->getRow(0)['storage']
            )
        );
    }


    public function testChangeExistingRecord()
    {
        $this->user->set('work', "I don't work and live with my mother");
        $this->assertEquals("I don't work and live with my mother", $this->user->get('work'));
        $this->assertEquals(
            "I don't work and live with my mother",
            unserialize(
                $this->getConnection()->createQueryTable('test', 'SELECT storage FROM users WHERE id=42')
                    ->getRow(0)['storage']
            )['work']
        );
    }


    public function testGetInstanceUserNotExists()
    {
        $this->setExpectedException('LogicException', "Can't find user with such id");
        User::getInstance(666);
    }

}
