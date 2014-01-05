<?php

namespace wiosloCMS\PhotoBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'UserRate' table.
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
class UserRateTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.wiosloCMS.PhotoBundle.Model.map.UserRateTableMap';

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
        $this->setName('UserRate');
        $this->setPhpName('UserRate');
        $this->setClassname('wiosloCMS\\PhotoBundle\\Model\\UserRate');
        $this->setPackage('src.wiosloCMS.PhotoBundle.Model');
        $this->setUseIdGenerator(false);
        $this->setIsCrossRef(true);
        // columns
        $this->addForeignPrimaryKey('user_id', 'UserId', 'INTEGER' , 'User', 'id', true, null, null);
        $this->addForeignPrimaryKey('photo_id', 'PhotoId', 'INTEGER' , 'PhotoRating', 'photo_id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Rating', 'wiosloCMS\\PhotoBundle\\Model\\Rating', RelationMap::MANY_TO_ONE, array('photo_id' => 'photo_id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('User', 'wiosloCMS\\UserBundle\\Model\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), 'CASCADE', 'CASCADE');
    } // buildRelations()

} // UserRateTableMap
