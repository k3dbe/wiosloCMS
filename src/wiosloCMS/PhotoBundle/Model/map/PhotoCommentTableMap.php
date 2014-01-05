<?php

namespace wiosloCMS\PhotoBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'PhotoComment' table.
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
class PhotoCommentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.wiosloCMS.PhotoBundle.Model.map.PhotoCommentTableMap';

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
        $this->setName('PhotoComment');
        $this->setPhpName('PhotoComment');
        $this->setClassname('wiosloCMS\\PhotoBundle\\Model\\PhotoComment');
        $this->setPackage('src.wiosloCMS.PhotoBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('user_id', 'UserId', 'INTEGER', 'User', 'id', true, null, null);
        $this->addForeignKey('photo_id', 'PhotoId', 'INTEGER', 'Photo', 'id', true, null, null);
        $this->addColumn('text', 'Text', 'VARCHAR', true, 500, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'wiosloCMS\\UserBundle\\Model\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Photo', 'wiosloCMS\\PhotoBundle\\Model\\Photo', RelationMap::MANY_TO_ONE, array('photo_id' => 'id', ), 'CASCADE', 'CASCADE');
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
  'disable_updated_at' => 'true',
),
        );
    } // getBehaviors()

} // PhotoCommentTableMap
