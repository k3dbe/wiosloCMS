<?php

namespace wiosloCMS\UserBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'User' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.wiosloCMS.UserBundle.Model.map
 */
class UserTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.wiosloCMS.UserBundle.Model.map.UserTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('User');
        $this->setPhpName('User');
        $this->setClassname('wiosloCMS\\UserBundle\\Model\\User');
        $this->setPackage('src.wiosloCMS.UserBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('uri', 'Uri', 'VARCHAR', true, 250, null);
        $this->addColumn('username', 'Username', 'VARCHAR', true, 50, null);
        $this->addColumn('email', 'Email', 'VARCHAR', true, 150, null);
        $this->addColumn('password', 'Password', 'VARCHAR', false, 130, null);
        $this->addColumn('registered_at', 'RegisteredAt', 'TIMESTAMP', true, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Photo', 'wiosloCMS\\PhotoBundle\\Model\\Photo', RelationMap::ONE_TO_MANY, array('id' => 'owner_id', ), 'CASCADE', 'CASCADE', 'Photos');
        $this->addRelation('PhotoComment', 'wiosloCMS\\PhotoBundle\\Model\\PhotoComment', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', 'CASCADE', 'PhotoComments');
        $this->addRelation('UserRate', 'wiosloCMS\\PhotoBundle\\Model\\UserRate', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', 'CASCADE', 'UserRates');
        $this->addRelation('Settings', 'wiosloCMS\\UserBundle\\Model\\Settings', RelationMap::ONE_TO_ONE, array('id' => 'user_id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('UserRole', 'wiosloCMS\\UserBundle\\Model\\UserRole', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', 'CASCADE', 'UserRoles');
        $this->addRelation('Rating', 'wiosloCMS\\PhotoBundle\\Model\\Rating', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Ratings');
        $this->addRelation('Role', 'wiosloCMS\\UserBundle\\Model\\Role', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Roles');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'sluggable' =>  array (
  'add_cleanup' => 'true',
  'slug_column' => 'uri',
  'slug_pattern' => '{username}',
  'replace_pattern' => '/[^\\w\\/]+/u',
  'replacement' => '-',
  'separator' => '_',
  'permanent' => 'true',
  'scope_column' => '',
),
            'timestampable' =>  array (
  'create_column' => 'registered_at',
  'update_column' => 'updated_at',
  'disable_updated_at' => 'false',
),
        );
    } // getBehaviors()

} // UserTableMap
