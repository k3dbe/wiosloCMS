<?php

namespace wiosloCMS\UserBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'UserRole' table.
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
class UserRoleTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.wiosloCMS.UserBundle.Model.map.UserRoleTableMap';

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
        $this->setName('UserRole');
        $this->setPhpName('UserRole');
        $this->setClassname('wiosloCMS\\UserBundle\\Model\\UserRole');
        $this->setPackage('src.wiosloCMS.UserBundle.Model');
        $this->setUseIdGenerator(false);
        $this->setIsCrossRef(true);
        // columns
        $this->addForeignPrimaryKey('user_id', 'UserId', 'INTEGER' , 'User', 'id', true, null, null);
        $this->addForeignPrimaryKey('role_id', 'RoleId', 'INTEGER' , 'Role', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'wiosloCMS\\UserBundle\\Model\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Role', 'wiosloCMS\\UserBundle\\Model\\Role', RelationMap::MANY_TO_ONE, array('role_id' => 'id', ), 'CASCADE', 'CASCADE');
    } // buildRelations()

} // UserRoleTableMap
