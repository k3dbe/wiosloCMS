<?php

namespace wiosloCMS\PhotoBundle\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use wiosloCMS\PhotoBundle\Model\Photo;
use wiosloCMS\PhotoBundle\Model\Rating;
use wiosloCMS\PhotoBundle\Model\RatingPeer;
use wiosloCMS\PhotoBundle\Model\RatingQuery;
use wiosloCMS\PhotoBundle\Model\UserRate;
use wiosloCMS\UserBundle\Model\User;

/**
 * @method RatingQuery orderByPhotoId($order = Criteria::ASC) Order by the photo_id column
 * @method RatingQuery orderByPlus($order = Criteria::ASC) Order by the plus column
 * @method RatingQuery orderByMinus($order = Criteria::ASC) Order by the minus column
 *
 * @method RatingQuery groupByPhotoId() Group by the photo_id column
 * @method RatingQuery groupByPlus() Group by the plus column
 * @method RatingQuery groupByMinus() Group by the minus column
 *
 * @method RatingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method RatingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method RatingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method RatingQuery leftJoinPhoto($relationAlias = null) Adds a LEFT JOIN clause to the query using the Photo relation
 * @method RatingQuery rightJoinPhoto($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Photo relation
 * @method RatingQuery innerJoinPhoto($relationAlias = null) Adds a INNER JOIN clause to the query using the Photo relation
 *
 * @method RatingQuery leftJoinUserRate($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserRate relation
 * @method RatingQuery rightJoinUserRate($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserRate relation
 * @method RatingQuery innerJoinUserRate($relationAlias = null) Adds a INNER JOIN clause to the query using the UserRate relation
 *
 * @method Rating findOne(PropelPDO $con = null) Return the first Rating matching the query
 * @method Rating findOneOrCreate(PropelPDO $con = null) Return the first Rating matching the query, or a new Rating object populated from the query conditions when no match is found
 *
 * @method Rating findOneByPlus(int $plus) Return the first Rating filtered by the plus column
 * @method Rating findOneByMinus(int $minus) Return the first Rating filtered by the minus column
 *
 * @method array findByPhotoId(int $photo_id) Return Rating objects filtered by the photo_id column
 * @method array findByPlus(int $plus) Return Rating objects filtered by the plus column
 * @method array findByMinus(int $minus) Return Rating objects filtered by the minus column
 */
abstract class BaseRatingQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseRatingQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'main';
        }
        if (null === $modelName) {
            $modelName = 'wiosloCMS\\PhotoBundle\\Model\\Rating';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RatingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   RatingQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RatingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RatingQuery) {
            return $criteria;
        }
        $query = new RatingQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Rating|Rating[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RatingPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RatingPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Rating A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByPhotoId($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Rating A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `photo_id`, `plus`, `minus` FROM `PhotoRating` WHERE `photo_id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Rating();
            $obj->hydrate($row);
            RatingPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Rating|Rating[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Rating[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return RatingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RatingPeer::PHOTO_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RatingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RatingPeer::PHOTO_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the photo_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPhotoId(1234); // WHERE photo_id = 1234
     * $query->filterByPhotoId(array(12, 34)); // WHERE photo_id IN (12, 34)
     * $query->filterByPhotoId(array('min' => 12)); // WHERE photo_id >= 12
     * $query->filterByPhotoId(array('max' => 12)); // WHERE photo_id <= 12
     * </code>
     *
     * @see       filterByPhoto()
     *
     * @param     mixed $photoId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RatingQuery The current query, for fluid interface
     */
    public function filterByPhotoId($photoId = null, $comparison = null)
    {
        if (is_array($photoId)) {
            $useMinMax = false;
            if (isset($photoId['min'])) {
                $this->addUsingAlias(RatingPeer::PHOTO_ID, $photoId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($photoId['max'])) {
                $this->addUsingAlias(RatingPeer::PHOTO_ID, $photoId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RatingPeer::PHOTO_ID, $photoId, $comparison);
    }

    /**
     * Filter the query on the plus column
     *
     * Example usage:
     * <code>
     * $query->filterByPlus(1234); // WHERE plus = 1234
     * $query->filterByPlus(array(12, 34)); // WHERE plus IN (12, 34)
     * $query->filterByPlus(array('min' => 12)); // WHERE plus >= 12
     * $query->filterByPlus(array('max' => 12)); // WHERE plus <= 12
     * </code>
     *
     * @param     mixed $plus The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RatingQuery The current query, for fluid interface
     */
    public function filterByPlus($plus = null, $comparison = null)
    {
        if (is_array($plus)) {
            $useMinMax = false;
            if (isset($plus['min'])) {
                $this->addUsingAlias(RatingPeer::PLUS, $plus['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($plus['max'])) {
                $this->addUsingAlias(RatingPeer::PLUS, $plus['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RatingPeer::PLUS, $plus, $comparison);
    }

    /**
     * Filter the query on the minus column
     *
     * Example usage:
     * <code>
     * $query->filterByMinus(1234); // WHERE minus = 1234
     * $query->filterByMinus(array(12, 34)); // WHERE minus IN (12, 34)
     * $query->filterByMinus(array('min' => 12)); // WHERE minus >= 12
     * $query->filterByMinus(array('max' => 12)); // WHERE minus <= 12
     * </code>
     *
     * @param     mixed $minus The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RatingQuery The current query, for fluid interface
     */
    public function filterByMinus($minus = null, $comparison = null)
    {
        if (is_array($minus)) {
            $useMinMax = false;
            if (isset($minus['min'])) {
                $this->addUsingAlias(RatingPeer::MINUS, $minus['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($minus['max'])) {
                $this->addUsingAlias(RatingPeer::MINUS, $minus['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RatingPeer::MINUS, $minus, $comparison);
    }

    /**
     * Filter the query by a related Photo object
     *
     * @param   Photo|PropelObjectCollection $photo The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RatingQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPhoto($photo, $comparison = null)
    {
        if ($photo instanceof Photo) {
            return $this
                ->addUsingAlias(RatingPeer::PHOTO_ID, $photo->getId(), $comparison);
        } elseif ($photo instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RatingPeer::PHOTO_ID, $photo->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPhoto() only accepts arguments of type Photo or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Photo relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RatingQuery The current query, for fluid interface
     */
    public function joinPhoto($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Photo');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Photo');
        }

        return $this;
    }

    /**
     * Use the Photo relation Photo object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \wiosloCMS\PhotoBundle\Model\PhotoQuery A secondary query class using the current class as primary query
     */
    public function usePhotoQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPhoto($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Photo', '\wiosloCMS\PhotoBundle\Model\PhotoQuery');
    }

    /**
     * Filter the query by a related UserRate object
     *
     * @param   UserRate|PropelObjectCollection $userRate  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RatingQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByUserRate($userRate, $comparison = null)
    {
        if ($userRate instanceof UserRate) {
            return $this
                ->addUsingAlias(RatingPeer::PHOTO_ID, $userRate->getPhotoId(), $comparison);
        } elseif ($userRate instanceof PropelObjectCollection) {
            return $this
                ->useUserRateQuery()
                ->filterByPrimaryKeys($userRate->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserRate() only accepts arguments of type UserRate or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserRate relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RatingQuery The current query, for fluid interface
     */
    public function joinUserRate($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserRate');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'UserRate');
        }

        return $this;
    }

    /**
     * Use the UserRate relation UserRate object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \wiosloCMS\PhotoBundle\Model\UserRateQuery A secondary query class using the current class as primary query
     */
    public function useUserRateQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserRate($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserRate', '\wiosloCMS\PhotoBundle\Model\UserRateQuery');
    }

    /**
     * Filter the query by a related User object
     * using the UserRate table as cross reference
     *
     * @param   User $user the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   RatingQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useUserRateQuery()
            ->filterByUser($user, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Rating $rating Object to remove from the list of results
     *
     * @return RatingQuery The current query, for fluid interface
     */
    public function prune($rating = null)
    {
        if ($rating) {
            $this->addUsingAlias(RatingPeer::PHOTO_ID, $rating->getPhotoId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
