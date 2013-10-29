<?php

namespace wiosloCMS\UserBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'Role' table.
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
class RoleTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.wiosloCMS.UserBundle.Model.map.RoleTableMap';

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
        $this->setName('Role');
        $this->setPhpName('Role');
        $this->setClassname('wiosloCMS\\UserBundle\\Model\\Role');
        $this->setPackage('src.wiosloCMS.UserBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 50, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('UserRole', 'wiosloCMS\\UserBundle\\Model\\UserRole', RelationMap::ONE_TO_MANY, array('id' => 'role_id', ), 'CASCADE', 'CASCADE', 'UserRoles');
        $this->addRelation('User', 'wiosloCMS\\UserBundle\\Model\\User', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Users');
    } // buildRelations()

} // RoleTableMap
