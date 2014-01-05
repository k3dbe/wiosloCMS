<?php

namespace wiosloCMS\PhotoBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'PhotoRating' table.
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
class RatingTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.wiosloCMS.PhotoBundle.Model.map.RatingTableMap';

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
        $this->setName('PhotoRating');
        $this->setPhpName('Rating');
        $this->setClassname('wiosloCMS\\PhotoBundle\\Model\\Rating');
        $this->setPackage('src.wiosloCMS.PhotoBundle.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('photo_id', 'PhotoId', 'INTEGER' , 'Photo', 'id', true, null, null);
        $this->addColumn('plus', 'Plus', 'INTEGER', true, 10, null);
        $this->addColumn('minus', 'Minus', 'INTEGER', true, 10, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Photo', 'wiosloCMS\\PhotoBundle\\Model\\Photo', RelationMap::MANY_TO_ONE, array('photo_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('UserRate', 'wiosloCMS\\PhotoBundle\\Model\\UserRate', RelationMap::ONE_TO_MANY, array('photo_id' => 'photo_id', ), 'CASCADE', 'CASCADE', 'UserRates');
        $this->addRelation('User', 'wiosloCMS\\UserBundle\\Model\\User', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Users');
    } // buildRelations()

} // RatingTableMap
