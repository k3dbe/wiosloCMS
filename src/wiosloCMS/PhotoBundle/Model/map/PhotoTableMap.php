<?php

namespace wiosloCMS\PhotoBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'Photo' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.wiosloCMS.PhotoBundle.Model.map
 */
class PhotoTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.wiosloCMS.PhotoBundle.Model.map.PhotoTableMap';

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
        $this->setName('Photo');
        $this->setPhpName('Photo');
        $this->setClassname('wiosloCMS\\PhotoBundle\\Model\\Photo');
        $this->setPackage('src.wiosloCMS.PhotoBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('uri', 'Uri', 'VARCHAR', true, 250, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 50, null);
        $this->addForeignKey('owner_id', 'OwnerId', 'INTEGER', 'User', 'id', true, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'wiosloCMS\\UserBundle\\Model\\User', RelationMap::MANY_TO_ONE, array('owner_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Rating', 'wiosloCMS\\PhotoBundle\\Model\\Rating', RelationMap::ONE_TO_ONE, array('id' => 'photo_id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('PhotoComment', 'wiosloCMS\\PhotoBundle\\Model\\PhotoComment', RelationMap::ONE_TO_MANY, array('id' => 'photo_id', ), 'CASCADE', 'CASCADE', 'PhotoComments');
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
            'timestampable' =>  array (
  'create_column' => 'created_at',
  'update_column' => 'updated_at',
  'disable_updated_at' => 'false',
),
        );
    } // getBehaviors()

} // PhotoTableMap
