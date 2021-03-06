<?php

namespace wiosloCMS\UserBundle\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use wiosloCMS\PhotoBundle\Model\Photo;
use wiosloCMS\PhotoBundle\Model\PhotoComment;
use wiosloCMS\PhotoBundle\Model\PhotoCommentQuery;
use wiosloCMS\PhotoBundle\Model\PhotoQuery;
use wiosloCMS\PhotoBundle\Model\Rating;
use wiosloCMS\PhotoBundle\Model\RatingQuery;
use wiosloCMS\PhotoBundle\Model\UserRate;
use wiosloCMS\PhotoBundle\Model\UserRateQuery;
use wiosloCMS\UserBundle\Model\Role;
use wiosloCMS\UserBundle\Model\RoleQuery;
use wiosloCMS\UserBundle\Model\Settings;
use wiosloCMS\UserBundle\Model\SettingsQuery;
use wiosloCMS\UserBundle\Model\User;
use wiosloCMS\UserBundle\Model\UserPeer;
use wiosloCMS\UserBundle\Model\UserQuery;
use wiosloCMS\UserBundle\Model\UserRole;
use wiosloCMS\UserBundle\Model\UserRoleQuery;

abstract class BaseUser extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'wiosloCMS\\UserBundle\\Model\\UserPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        UserPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the uri field.
     * @var        string
     */
    protected $uri;

    /**
     * The value for the username field.
     * @var        string
     */
    protected $username;

    /**
     * The value for the email field.
     * @var        string
     */
    protected $email;

    /**
     * The value for the password field.
     * @var        string
     */
    protected $password;

    /**
     * The value for the registered_at field.
     * @var        string
     */
    protected $registered_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        PropelObjectCollection|Photo[] Collection to store aggregation of Photo objects.
     */
    protected $collPhotos;
    protected $collPhotosPartial;

    /**
     * @var        PropelObjectCollection|PhotoComment[] Collection to store aggregation of PhotoComment objects.
     */
    protected $collPhotoComments;
    protected $collPhotoCommentsPartial;

    /**
     * @var        PropelObjectCollection|UserRate[] Collection to store aggregation of UserRate objects.
     */
    protected $collUserRates;
    protected $collUserRatesPartial;

    /**
     * @var        Settings one-to-one related Settings object
     */
    protected $singleSettings;

    /**
     * @var        PropelObjectCollection|UserRole[] Collection to store aggregation of UserRole objects.
     */
    protected $collUserRoles;
    protected $collUserRolesPartial;

    /**
     * @var        PropelObjectCollection|Rating[] Collection to store aggregation of Rating objects.
     */
    protected $collRatings;

    /**
     * @var        PropelObjectCollection|Role[] Collection to store aggregation of Role objects.
     */
    protected $collRoles;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ratingsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $rolesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $photosScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $photoCommentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $userRatesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $userRolesScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [uri] column value.
     *
     * @return string
     */
    public function getUri()
    {

        return $this->uri;
    }

    /**
     * Get the [username] column value.
     *
     * @return string
     */
    public function getUsername()
    {

        return $this->username;
    }

    /**
     * Get the [email] column value.
     *
     * @return string
     */
    public function getEmail()
    {

        return $this->email;
    }

    /**
     * Get the [password] column value.
     *
     * @return string
     */
    public function getPassword()
    {

        return $this->password;
    }

    /**
     * Get the [optionally formatted] temporal [registered_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getRegisteredAt($format = null)
    {
        if ($this->registered_at === null) {
            return null;
        }

        if ($this->registered_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->registered_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->registered_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = null)
    {
        if ($this->updated_at === null) {
            return null;
        }

        if ($this->updated_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->updated_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return User The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = UserPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [uri] column.
     *
     * @param  string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setUri($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->uri !== $v) {
            $this->uri = $v;
            $this->modifiedColumns[] = UserPeer::URI;
        }


        return $this;
    } // setUri()

    /**
     * Set the value of [username] column.
     *
     * @param  string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setUsername($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->username !== $v) {
            $this->username = $v;
            $this->modifiedColumns[] = UserPeer::USERNAME;
        }


        return $this;
    } // setUsername()

    /**
     * Set the value of [email] column.
     *
     * @param  string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = UserPeer::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [password] column.
     *
     * @param  string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setPassword($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->password !== $v) {
            $this->password = $v;
            $this->modifiedColumns[] = UserPeer::PASSWORD;
        }


        return $this;
    } // setPassword()

    /**
     * Sets the value of [registered_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return User The current object (for fluent API support)
     */
    public function setRegisteredAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->registered_at !== null || $dt !== null) {
            $currentDateAsString = ($this->registered_at !== null && $tmpDt = new DateTime($this->registered_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->registered_at = $newDateAsString;
                $this->modifiedColumns[] = UserPeer::REGISTERED_AT;
            }
        } // if either are not null


        return $this;
    } // setRegisteredAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return User The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = UserPeer::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->uri = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->username = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->email = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->password = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->registered_at = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->updated_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 7; // 7 = UserPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating User object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = UserPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collPhotos = null;

            $this->collPhotoComments = null;

            $this->collUserRates = null;

            $this->singleSettings = null;

            $this->collUserRoles = null;

            $this->collRatings = null;
            $this->collRoles = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = UserQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            // sluggable behavior

            if ($this->isColumnModified(UserPeer::URI) && $this->getUri()) {
                $this->setUri($this->makeSlugUnique($this->getUri()));
            } elseif (!$this->getUri()) {
                $this->setUri($this->createSlug());
            }
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(UserPeer::REGISTERED_AT)) {
                    $this->setRegisteredAt(time());
                }
                if (!$this->isColumnModified(UserPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(UserPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                UserPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->ratingsScheduledForDeletion !== null) {
                if (!$this->ratingsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->ratingsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    UserRateQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->ratingsScheduledForDeletion = null;
                }

                foreach ($this->getRatings() as $rating) {
                    if ($rating->isModified()) {
                        $rating->save($con);
                    }
                }
            } elseif ($this->collRatings) {
                foreach ($this->collRatings as $rating) {
                    if ($rating->isModified()) {
                        $rating->save($con);
                    }
                }
            }

            if ($this->rolesScheduledForDeletion !== null) {
                if (!$this->rolesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->rolesScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    UserRoleQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->rolesScheduledForDeletion = null;
                }

                foreach ($this->getRoles() as $role) {
                    if ($role->isModified()) {
                        $role->save($con);
                    }
                }
            } elseif ($this->collRoles) {
                foreach ($this->collRoles as $role) {
                    if ($role->isModified()) {
                        $role->save($con);
                    }
                }
            }

            if ($this->photosScheduledForDeletion !== null) {
                if (!$this->photosScheduledForDeletion->isEmpty()) {
                    PhotoQuery::create()
                        ->filterByPrimaryKeys($this->photosScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->photosScheduledForDeletion = null;
                }
            }

            if ($this->collPhotos !== null) {
                foreach ($this->collPhotos as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->photoCommentsScheduledForDeletion !== null) {
                if (!$this->photoCommentsScheduledForDeletion->isEmpty()) {
                    PhotoCommentQuery::create()
                        ->filterByPrimaryKeys($this->photoCommentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->photoCommentsScheduledForDeletion = null;
                }
            }

            if ($this->collPhotoComments !== null) {
                foreach ($this->collPhotoComments as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userRatesScheduledForDeletion !== null) {
                if (!$this->userRatesScheduledForDeletion->isEmpty()) {
                    UserRateQuery::create()
                        ->filterByPrimaryKeys($this->userRatesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userRatesScheduledForDeletion = null;
                }
            }

            if ($this->collUserRates !== null) {
                foreach ($this->collUserRates as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->singleSettings !== null) {
                if (!$this->singleSettings->isDeleted() && ($this->singleSettings->isNew() || $this->singleSettings->isModified())) {
                        $affectedRows += $this->singleSettings->save($con);
                }
            }

            if ($this->userRolesScheduledForDeletion !== null) {
                if (!$this->userRolesScheduledForDeletion->isEmpty()) {
                    UserRoleQuery::create()
                        ->filterByPrimaryKeys($this->userRolesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userRolesScheduledForDeletion = null;
                }
            }

            if ($this->collUserRoles !== null) {
                foreach ($this->collUserRoles as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = UserPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(UserPeer::URI)) {
            $modifiedColumns[':p' . $index++]  = '`uri`';
        }
        if ($this->isColumnModified(UserPeer::USERNAME)) {
            $modifiedColumns[':p' . $index++]  = '`username`';
        }
        if ($this->isColumnModified(UserPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = '`email`';
        }
        if ($this->isColumnModified(UserPeer::PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = '`password`';
        }
        if ($this->isColumnModified(UserPeer::REGISTERED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`registered_at`';
        }
        if ($this->isColumnModified(UserPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `User` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`uri`':
                        $stmt->bindValue($identifier, $this->uri, PDO::PARAM_STR);
                        break;
                    case '`username`':
                        $stmt->bindValue($identifier, $this->username, PDO::PARAM_STR);
                        break;
                    case '`email`':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case '`password`':
                        $stmt->bindValue($identifier, $this->password, PDO::PARAM_STR);
                        break;
                    case '`registered_at`':
                        $stmt->bindValue($identifier, $this->registered_at, PDO::PARAM_STR);
                        break;
                    case '`updated_at`':
                        $stmt->bindValue($identifier, $this->updated_at, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = UserPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collPhotos !== null) {
                    foreach ($this->collPhotos as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collPhotoComments !== null) {
                    foreach ($this->collPhotoComments as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collUserRates !== null) {
                    foreach ($this->collUserRates as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->singleSettings !== null) {
                    if (!$this->singleSettings->validate($columns)) {
                        $failureMap = array_merge($failureMap, $this->singleSettings->getValidationFailures());
                    }
                }

                if ($this->collUserRoles !== null) {
                    foreach ($this->collUserRoles as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getUri();
                break;
            case 2:
                return $this->getUsername();
                break;
            case 3:
                return $this->getEmail();
                break;
            case 4:
                return $this->getPassword();
                break;
            case 5:
                return $this->getRegisteredAt();
                break;
            case 6:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['User'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['User'][$this->getPrimaryKey()] = true;
        $keys = UserPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUri(),
            $keys[2] => $this->getUsername(),
            $keys[3] => $this->getEmail(),
            $keys[4] => $this->getPassword(),
            $keys[5] => $this->getRegisteredAt(),
            $keys[6] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collPhotos) {
                $result['Photos'] = $this->collPhotos->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPhotoComments) {
                $result['PhotoComments'] = $this->collPhotoComments->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserRates) {
                $result['UserRates'] = $this->collUserRates->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->singleSettings) {
                $result['Settings'] = $this->singleSettings->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
            }
            if (null !== $this->collUserRoles) {
                $result['UserRoles'] = $this->collUserRoles->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setUri($value);
                break;
            case 2:
                $this->setUsername($value);
                break;
            case 3:
                $this->setEmail($value);
                break;
            case 4:
                $this->setPassword($value);
                break;
            case 5:
                $this->setRegisteredAt($value);
                break;
            case 6:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = UserPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUri($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setUsername($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setEmail($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setPassword($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setRegisteredAt($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setUpdatedAt($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserPeer::DATABASE_NAME);

        if ($this->isColumnModified(UserPeer::ID)) $criteria->add(UserPeer::ID, $this->id);
        if ($this->isColumnModified(UserPeer::URI)) $criteria->add(UserPeer::URI, $this->uri);
        if ($this->isColumnModified(UserPeer::USERNAME)) $criteria->add(UserPeer::USERNAME, $this->username);
        if ($this->isColumnModified(UserPeer::EMAIL)) $criteria->add(UserPeer::EMAIL, $this->email);
        if ($this->isColumnModified(UserPeer::PASSWORD)) $criteria->add(UserPeer::PASSWORD, $this->password);
        if ($this->isColumnModified(UserPeer::REGISTERED_AT)) $criteria->add(UserPeer::REGISTERED_AT, $this->registered_at);
        if ($this->isColumnModified(UserPeer::UPDATED_AT)) $criteria->add(UserPeer::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(UserPeer::DATABASE_NAME);
        $criteria->add(UserPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of User (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUri($this->getUri());
        $copyObj->setUsername($this->getUsername());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setPassword($this->getPassword());
        $copyObj->setRegisteredAt($this->getRegisteredAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getPhotos() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPhoto($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPhotoComments() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPhotoComment($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserRates() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserRate($relObj->copy($deepCopy));
                }
            }

            $relObj = $this->getSettings();
            if ($relObj) {
                $copyObj->setSettings($relObj->copy($deepCopy));
            }

            foreach ($this->getUserRoles() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserRole($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return User Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return UserPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new UserPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Photo' == $relationName) {
            $this->initPhotos();
        }
        if ('PhotoComment' == $relationName) {
            $this->initPhotoComments();
        }
        if ('UserRate' == $relationName) {
            $this->initUserRates();
        }
        if ('UserRole' == $relationName) {
            $this->initUserRoles();
        }
    }

    /**
     * Clears out the collPhotos collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addPhotos()
     */
    public function clearPhotos()
    {
        $this->collPhotos = null; // important to set this to null since that means it is uninitialized
        $this->collPhotosPartial = null;

        return $this;
    }

    /**
     * reset is the collPhotos collection loaded partially
     *
     * @return void
     */
    public function resetPartialPhotos($v = true)
    {
        $this->collPhotosPartial = $v;
    }

    /**
     * Initializes the collPhotos collection.
     *
     * By default this just sets the collPhotos collection to an empty array (like clearcollPhotos());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPhotos($overrideExisting = true)
    {
        if (null !== $this->collPhotos && !$overrideExisting) {
            return;
        }
        $this->collPhotos = new PropelObjectCollection();
        $this->collPhotos->setModel('Photo');
    }

    /**
     * Gets an array of Photo objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Photo[] List of Photo objects
     * @throws PropelException
     */
    public function getPhotos($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPhotosPartial && !$this->isNew();
        if (null === $this->collPhotos || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPhotos) {
                // return empty collection
                $this->initPhotos();
            } else {
                $collPhotos = PhotoQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPhotosPartial && count($collPhotos)) {
                      $this->initPhotos(false);

                      foreach ($collPhotos as $obj) {
                        if (false == $this->collPhotos->contains($obj)) {
                          $this->collPhotos->append($obj);
                        }
                      }

                      $this->collPhotosPartial = true;
                    }

                    $collPhotos->getInternalIterator()->rewind();

                    return $collPhotos;
                }

                if ($partial && $this->collPhotos) {
                    foreach ($this->collPhotos as $obj) {
                        if ($obj->isNew()) {
                            $collPhotos[] = $obj;
                        }
                    }
                }

                $this->collPhotos = $collPhotos;
                $this->collPhotosPartial = false;
            }
        }

        return $this->collPhotos;
    }

    /**
     * Sets a collection of Photo objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $photos A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setPhotos(PropelCollection $photos, PropelPDO $con = null)
    {
        $photosToDelete = $this->getPhotos(new Criteria(), $con)->diff($photos);


        $this->photosScheduledForDeletion = $photosToDelete;

        foreach ($photosToDelete as $photoRemoved) {
            $photoRemoved->setUser(null);
        }

        $this->collPhotos = null;
        foreach ($photos as $photo) {
            $this->addPhoto($photo);
        }

        $this->collPhotos = $photos;
        $this->collPhotosPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Photo objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Photo objects.
     * @throws PropelException
     */
    public function countPhotos(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPhotosPartial && !$this->isNew();
        if (null === $this->collPhotos || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPhotos) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPhotos());
            }
            $query = PhotoQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collPhotos);
    }

    /**
     * Method called to associate a Photo object to this object
     * through the Photo foreign key attribute.
     *
     * @param    Photo $l Photo
     * @return User The current object (for fluent API support)
     */
    public function addPhoto(Photo $l)
    {
        if ($this->collPhotos === null) {
            $this->initPhotos();
            $this->collPhotosPartial = true;
        }

        if (!in_array($l, $this->collPhotos->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPhoto($l);

            if ($this->photosScheduledForDeletion and $this->photosScheduledForDeletion->contains($l)) {
                $this->photosScheduledForDeletion->remove($this->photosScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Photo $photo The photo object to add.
     */
    protected function doAddPhoto($photo)
    {
        $this->collPhotos[]= $photo;
        $photo->setUser($this);
    }

    /**
     * @param	Photo $photo The photo object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removePhoto($photo)
    {
        if ($this->getPhotos()->contains($photo)) {
            $this->collPhotos->remove($this->collPhotos->search($photo));
            if (null === $this->photosScheduledForDeletion) {
                $this->photosScheduledForDeletion = clone $this->collPhotos;
                $this->photosScheduledForDeletion->clear();
            }
            $this->photosScheduledForDeletion[]= clone $photo;
            $photo->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collPhotoComments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addPhotoComments()
     */
    public function clearPhotoComments()
    {
        $this->collPhotoComments = null; // important to set this to null since that means it is uninitialized
        $this->collPhotoCommentsPartial = null;

        return $this;
    }

    /**
     * reset is the collPhotoComments collection loaded partially
     *
     * @return void
     */
    public function resetPartialPhotoComments($v = true)
    {
        $this->collPhotoCommentsPartial = $v;
    }

    /**
     * Initializes the collPhotoComments collection.
     *
     * By default this just sets the collPhotoComments collection to an empty array (like clearcollPhotoComments());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPhotoComments($overrideExisting = true)
    {
        if (null !== $this->collPhotoComments && !$overrideExisting) {
            return;
        }
        $this->collPhotoComments = new PropelObjectCollection();
        $this->collPhotoComments->setModel('PhotoComment');
    }

    /**
     * Gets an array of PhotoComment objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|PhotoComment[] List of PhotoComment objects
     * @throws PropelException
     */
    public function getPhotoComments($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPhotoCommentsPartial && !$this->isNew();
        if (null === $this->collPhotoComments || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPhotoComments) {
                // return empty collection
                $this->initPhotoComments();
            } else {
                $collPhotoComments = PhotoCommentQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPhotoCommentsPartial && count($collPhotoComments)) {
                      $this->initPhotoComments(false);

                      foreach ($collPhotoComments as $obj) {
                        if (false == $this->collPhotoComments->contains($obj)) {
                          $this->collPhotoComments->append($obj);
                        }
                      }

                      $this->collPhotoCommentsPartial = true;
                    }

                    $collPhotoComments->getInternalIterator()->rewind();

                    return $collPhotoComments;
                }

                if ($partial && $this->collPhotoComments) {
                    foreach ($this->collPhotoComments as $obj) {
                        if ($obj->isNew()) {
                            $collPhotoComments[] = $obj;
                        }
                    }
                }

                $this->collPhotoComments = $collPhotoComments;
                $this->collPhotoCommentsPartial = false;
            }
        }

        return $this->collPhotoComments;
    }

    /**
     * Sets a collection of PhotoComment objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $photoComments A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setPhotoComments(PropelCollection $photoComments, PropelPDO $con = null)
    {
        $photoCommentsToDelete = $this->getPhotoComments(new Criteria(), $con)->diff($photoComments);


        $this->photoCommentsScheduledForDeletion = $photoCommentsToDelete;

        foreach ($photoCommentsToDelete as $photoCommentRemoved) {
            $photoCommentRemoved->setUser(null);
        }

        $this->collPhotoComments = null;
        foreach ($photoComments as $photoComment) {
            $this->addPhotoComment($photoComment);
        }

        $this->collPhotoComments = $photoComments;
        $this->collPhotoCommentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PhotoComment objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related PhotoComment objects.
     * @throws PropelException
     */
    public function countPhotoComments(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPhotoCommentsPartial && !$this->isNew();
        if (null === $this->collPhotoComments || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPhotoComments) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPhotoComments());
            }
            $query = PhotoCommentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collPhotoComments);
    }

    /**
     * Method called to associate a PhotoComment object to this object
     * through the PhotoComment foreign key attribute.
     *
     * @param    PhotoComment $l PhotoComment
     * @return User The current object (for fluent API support)
     */
    public function addPhotoComment(PhotoComment $l)
    {
        if ($this->collPhotoComments === null) {
            $this->initPhotoComments();
            $this->collPhotoCommentsPartial = true;
        }

        if (!in_array($l, $this->collPhotoComments->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPhotoComment($l);

            if ($this->photoCommentsScheduledForDeletion and $this->photoCommentsScheduledForDeletion->contains($l)) {
                $this->photoCommentsScheduledForDeletion->remove($this->photoCommentsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	PhotoComment $photoComment The photoComment object to add.
     */
    protected function doAddPhotoComment($photoComment)
    {
        $this->collPhotoComments[]= $photoComment;
        $photoComment->setUser($this);
    }

    /**
     * @param	PhotoComment $photoComment The photoComment object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removePhotoComment($photoComment)
    {
        if ($this->getPhotoComments()->contains($photoComment)) {
            $this->collPhotoComments->remove($this->collPhotoComments->search($photoComment));
            if (null === $this->photoCommentsScheduledForDeletion) {
                $this->photoCommentsScheduledForDeletion = clone $this->collPhotoComments;
                $this->photoCommentsScheduledForDeletion->clear();
            }
            $this->photoCommentsScheduledForDeletion[]= clone $photoComment;
            $photoComment->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related PhotoComments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|PhotoComment[] List of PhotoComment objects
     */
    public function getPhotoCommentsJoinPhoto($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PhotoCommentQuery::create(null, $criteria);
        $query->joinWith('Photo', $join_behavior);

        return $this->getPhotoComments($query, $con);
    }

    /**
     * Clears out the collUserRates collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addUserRates()
     */
    public function clearUserRates()
    {
        $this->collUserRates = null; // important to set this to null since that means it is uninitialized
        $this->collUserRatesPartial = null;

        return $this;
    }

    /**
     * reset is the collUserRates collection loaded partially
     *
     * @return void
     */
    public function resetPartialUserRates($v = true)
    {
        $this->collUserRatesPartial = $v;
    }

    /**
     * Initializes the collUserRates collection.
     *
     * By default this just sets the collUserRates collection to an empty array (like clearcollUserRates());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserRates($overrideExisting = true)
    {
        if (null !== $this->collUserRates && !$overrideExisting) {
            return;
        }
        $this->collUserRates = new PropelObjectCollection();
        $this->collUserRates->setModel('UserRate');
    }

    /**
     * Gets an array of UserRate objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserRate[] List of UserRate objects
     * @throws PropelException
     */
    public function getUserRates($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collUserRatesPartial && !$this->isNew();
        if (null === $this->collUserRates || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserRates) {
                // return empty collection
                $this->initUserRates();
            } else {
                $collUserRates = UserRateQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collUserRatesPartial && count($collUserRates)) {
                      $this->initUserRates(false);

                      foreach ($collUserRates as $obj) {
                        if (false == $this->collUserRates->contains($obj)) {
                          $this->collUserRates->append($obj);
                        }
                      }

                      $this->collUserRatesPartial = true;
                    }

                    $collUserRates->getInternalIterator()->rewind();

                    return $collUserRates;
                }

                if ($partial && $this->collUserRates) {
                    foreach ($this->collUserRates as $obj) {
                        if ($obj->isNew()) {
                            $collUserRates[] = $obj;
                        }
                    }
                }

                $this->collUserRates = $collUserRates;
                $this->collUserRatesPartial = false;
            }
        }

        return $this->collUserRates;
    }

    /**
     * Sets a collection of UserRate objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $userRates A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setUserRates(PropelCollection $userRates, PropelPDO $con = null)
    {
        $userRatesToDelete = $this->getUserRates(new Criteria(), $con)->diff($userRates);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->userRatesScheduledForDeletion = clone $userRatesToDelete;

        foreach ($userRatesToDelete as $userRateRemoved) {
            $userRateRemoved->setUser(null);
        }

        $this->collUserRates = null;
        foreach ($userRates as $userRate) {
            $this->addUserRate($userRate);
        }

        $this->collUserRates = $userRates;
        $this->collUserRatesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserRate objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related UserRate objects.
     * @throws PropelException
     */
    public function countUserRates(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collUserRatesPartial && !$this->isNew();
        if (null === $this->collUserRates || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserRates) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserRates());
            }
            $query = UserRateQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collUserRates);
    }

    /**
     * Method called to associate a UserRate object to this object
     * through the UserRate foreign key attribute.
     *
     * @param    UserRate $l UserRate
     * @return User The current object (for fluent API support)
     */
    public function addUserRate(UserRate $l)
    {
        if ($this->collUserRates === null) {
            $this->initUserRates();
            $this->collUserRatesPartial = true;
        }

        if (!in_array($l, $this->collUserRates->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserRate($l);

            if ($this->userRatesScheduledForDeletion and $this->userRatesScheduledForDeletion->contains($l)) {
                $this->userRatesScheduledForDeletion->remove($this->userRatesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	UserRate $userRate The userRate object to add.
     */
    protected function doAddUserRate($userRate)
    {
        $this->collUserRates[]= $userRate;
        $userRate->setUser($this);
    }

    /**
     * @param	UserRate $userRate The userRate object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeUserRate($userRate)
    {
        if ($this->getUserRates()->contains($userRate)) {
            $this->collUserRates->remove($this->collUserRates->search($userRate));
            if (null === $this->userRatesScheduledForDeletion) {
                $this->userRatesScheduledForDeletion = clone $this->collUserRates;
                $this->userRatesScheduledForDeletion->clear();
            }
            $this->userRatesScheduledForDeletion[]= clone $userRate;
            $userRate->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related UserRates from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|UserRate[] List of UserRate objects
     */
    public function getUserRatesJoinRating($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = UserRateQuery::create(null, $criteria);
        $query->joinWith('Rating', $join_behavior);

        return $this->getUserRates($query, $con);
    }

    /**
     * Gets a single Settings object, which is related to this object by a one-to-one relationship.
     *
     * @param PropelPDO $con optional connection object
     * @return Settings
     * @throws PropelException
     */
    public function getSettings(PropelPDO $con = null)
    {

        if ($this->singleSettings === null && !$this->isNew()) {
            $this->singleSettings = SettingsQuery::create()->findPk($this->getPrimaryKey(), $con);
        }

        return $this->singleSettings;
    }

    /**
     * Sets a single Settings object as related to this object by a one-to-one relationship.
     *
     * @param                  Settings $v Settings
     * @return User The current object (for fluent API support)
     * @throws PropelException
     */
    public function setSettings(Settings $v = null)
    {
        $this->singleSettings = $v;

        // Make sure that that the passed-in Settings isn't already associated with this object
        if ($v !== null && $v->getUser(null, false) === null) {
            $v->setUser($this);
        }

        return $this;
    }

    /**
     * Clears out the collUserRoles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addUserRoles()
     */
    public function clearUserRoles()
    {
        $this->collUserRoles = null; // important to set this to null since that means it is uninitialized
        $this->collUserRolesPartial = null;

        return $this;
    }

    /**
     * reset is the collUserRoles collection loaded partially
     *
     * @return void
     */
    public function resetPartialUserRoles($v = true)
    {
        $this->collUserRolesPartial = $v;
    }

    /**
     * Initializes the collUserRoles collection.
     *
     * By default this just sets the collUserRoles collection to an empty array (like clearcollUserRoles());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserRoles($overrideExisting = true)
    {
        if (null !== $this->collUserRoles && !$overrideExisting) {
            return;
        }
        $this->collUserRoles = new PropelObjectCollection();
        $this->collUserRoles->setModel('UserRole');
    }

    /**
     * Gets an array of UserRole objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserRole[] List of UserRole objects
     * @throws PropelException
     */
    public function getUserRoles($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collUserRolesPartial && !$this->isNew();
        if (null === $this->collUserRoles || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserRoles) {
                // return empty collection
                $this->initUserRoles();
            } else {
                $collUserRoles = UserRoleQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collUserRolesPartial && count($collUserRoles)) {
                      $this->initUserRoles(false);

                      foreach ($collUserRoles as $obj) {
                        if (false == $this->collUserRoles->contains($obj)) {
                          $this->collUserRoles->append($obj);
                        }
                      }

                      $this->collUserRolesPartial = true;
                    }

                    $collUserRoles->getInternalIterator()->rewind();

                    return $collUserRoles;
                }

                if ($partial && $this->collUserRoles) {
                    foreach ($this->collUserRoles as $obj) {
                        if ($obj->isNew()) {
                            $collUserRoles[] = $obj;
                        }
                    }
                }

                $this->collUserRoles = $collUserRoles;
                $this->collUserRolesPartial = false;
            }
        }

        return $this->collUserRoles;
    }

    /**
     * Sets a collection of UserRole objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $userRoles A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setUserRoles(PropelCollection $userRoles, PropelPDO $con = null)
    {
        $userRolesToDelete = $this->getUserRoles(new Criteria(), $con)->diff($userRoles);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->userRolesScheduledForDeletion = clone $userRolesToDelete;

        foreach ($userRolesToDelete as $userRoleRemoved) {
            $userRoleRemoved->setUser(null);
        }

        $this->collUserRoles = null;
        foreach ($userRoles as $userRole) {
            $this->addUserRole($userRole);
        }

        $this->collUserRoles = $userRoles;
        $this->collUserRolesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserRole objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related UserRole objects.
     * @throws PropelException
     */
    public function countUserRoles(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collUserRolesPartial && !$this->isNew();
        if (null === $this->collUserRoles || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserRoles) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserRoles());
            }
            $query = UserRoleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collUserRoles);
    }

    /**
     * Method called to associate a UserRole object to this object
     * through the UserRole foreign key attribute.
     *
     * @param    UserRole $l UserRole
     * @return User The current object (for fluent API support)
     */
    public function addUserRole(UserRole $l)
    {
        if ($this->collUserRoles === null) {
            $this->initUserRoles();
            $this->collUserRolesPartial = true;
        }

        if (!in_array($l, $this->collUserRoles->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserRole($l);

            if ($this->userRolesScheduledForDeletion and $this->userRolesScheduledForDeletion->contains($l)) {
                $this->userRolesScheduledForDeletion->remove($this->userRolesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	UserRole $userRole The userRole object to add.
     */
    protected function doAddUserRole($userRole)
    {
        $this->collUserRoles[]= $userRole;
        $userRole->setUser($this);
    }

    /**
     * @param	UserRole $userRole The userRole object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeUserRole($userRole)
    {
        if ($this->getUserRoles()->contains($userRole)) {
            $this->collUserRoles->remove($this->collUserRoles->search($userRole));
            if (null === $this->userRolesScheduledForDeletion) {
                $this->userRolesScheduledForDeletion = clone $this->collUserRoles;
                $this->userRolesScheduledForDeletion->clear();
            }
            $this->userRolesScheduledForDeletion[]= clone $userRole;
            $userRole->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related UserRoles from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|UserRole[] List of UserRole objects
     */
    public function getUserRolesJoinRole($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = UserRoleQuery::create(null, $criteria);
        $query->joinWith('Role', $join_behavior);

        return $this->getUserRoles($query, $con);
    }

    /**
     * Clears out the collRatings collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addRatings()
     */
    public function clearRatings()
    {
        $this->collRatings = null; // important to set this to null since that means it is uninitialized
        $this->collRatingsPartial = null;

        return $this;
    }

    /**
     * Initializes the collRatings collection.
     *
     * By default this just sets the collRatings collection to an empty collection (like clearRatings());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initRatings()
    {
        $this->collRatings = new PropelObjectCollection();
        $this->collRatings->setModel('Rating');
    }

    /**
     * Gets a collection of Rating objects related by a many-to-many relationship
     * to the current object by way of the UserRate cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Rating[] List of Rating objects
     */
    public function getRatings($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collRatings || null !== $criteria) {
            if ($this->isNew() && null === $this->collRatings) {
                // return empty collection
                $this->initRatings();
            } else {
                $collRatings = RatingQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collRatings;
                }
                $this->collRatings = $collRatings;
            }
        }

        return $this->collRatings;
    }

    /**
     * Sets a collection of Rating objects related by a many-to-many relationship
     * to the current object by way of the UserRate cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ratings A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setRatings(PropelCollection $ratings, PropelPDO $con = null)
    {
        $this->clearRatings();
        $currentRatings = $this->getRatings(null, $con);

        $this->ratingsScheduledForDeletion = $currentRatings->diff($ratings);

        foreach ($ratings as $rating) {
            if (!$currentRatings->contains($rating)) {
                $this->doAddRating($rating);
            }
        }

        $this->collRatings = $ratings;

        return $this;
    }

    /**
     * Gets the number of Rating objects related by a many-to-many relationship
     * to the current object by way of the UserRate cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Rating objects
     */
    public function countRatings($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collRatings || null !== $criteria) {
            if ($this->isNew() && null === $this->collRatings) {
                return 0;
            } else {
                $query = RatingQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collRatings);
        }
    }

    /**
     * Associate a Rating object to this object
     * through the UserRate cross reference table.
     *
     * @param  Rating $rating The UserRate object to relate
     * @return User The current object (for fluent API support)
     */
    public function addRating(Rating $rating)
    {
        if ($this->collRatings === null) {
            $this->initRatings();
        }

        if (!$this->collRatings->contains($rating)) { // only add it if the **same** object is not already associated
            $this->doAddRating($rating);
            $this->collRatings[] = $rating;

            if ($this->ratingsScheduledForDeletion and $this->ratingsScheduledForDeletion->contains($rating)) {
                $this->ratingsScheduledForDeletion->remove($this->ratingsScheduledForDeletion->search($rating));
            }
        }

        return $this;
    }

    /**
     * @param	Rating $rating The rating object to add.
     */
    protected function doAddRating(Rating $rating)
    {
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$rating->getUsers()->contains($this)) {
            $userRate = new UserRate();
            $userRate->setRating($rating);
            $this->addUserRate($userRate);

            $foreignCollection = $rating->getUsers();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a Rating object to this object
     * through the UserRate cross reference table.
     *
     * @param Rating $rating The UserRate object to relate
     * @return User The current object (for fluent API support)
     */
    public function removeRating(Rating $rating)
    {
        if ($this->getRatings()->contains($rating)) {
            $this->collRatings->remove($this->collRatings->search($rating));
            if (null === $this->ratingsScheduledForDeletion) {
                $this->ratingsScheduledForDeletion = clone $this->collRatings;
                $this->ratingsScheduledForDeletion->clear();
            }
            $this->ratingsScheduledForDeletion[]= $rating;
        }

        return $this;
    }

    /**
     * Clears out the collRoles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addRoles()
     */
    public function clearRoles()
    {
        $this->collRoles = null; // important to set this to null since that means it is uninitialized
        $this->collRolesPartial = null;

        return $this;
    }

    /**
     * Initializes the collRoles collection.
     *
     * By default this just sets the collRoles collection to an empty collection (like clearRoles());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initRoles()
    {
        $this->collRoles = new PropelObjectCollection();
        $this->collRoles->setModel('Role');
    }

    /**
     * Gets a collection of Role objects related by a many-to-many relationship
     * to the current object by way of the UserRole cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Role[] List of Role objects
     */
    public function getRoles($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collRoles || null !== $criteria) {
            if ($this->isNew() && null === $this->collRoles) {
                // return empty collection
                $this->initRoles();
            } else {
                $collRoles = RoleQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collRoles;
                }
                $this->collRoles = $collRoles;
            }
        }

        return $this->collRoles;
    }

    /**
     * Sets a collection of Role objects related by a many-to-many relationship
     * to the current object by way of the UserRole cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $roles A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setRoles(PropelCollection $roles, PropelPDO $con = null)
    {
        $this->clearRoles();
        $currentRoles = $this->getRoles(null, $con);

        $this->rolesScheduledForDeletion = $currentRoles->diff($roles);

        foreach ($roles as $role) {
            if (!$currentRoles->contains($role)) {
                $this->doAddRole($role);
            }
        }

        $this->collRoles = $roles;

        return $this;
    }

    /**
     * Gets the number of Role objects related by a many-to-many relationship
     * to the current object by way of the UserRole cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Role objects
     */
    public function countRoles($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collRoles || null !== $criteria) {
            if ($this->isNew() && null === $this->collRoles) {
                return 0;
            } else {
                $query = RoleQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collRoles);
        }
    }

    /**
     * Associate a Role object to this object
     * through the UserRole cross reference table.
     *
     * @param  Role $role The UserRole object to relate
     * @return User The current object (for fluent API support)
     */
    public function addRole(Role $role)
    {
        if ($this->collRoles === null) {
            $this->initRoles();
        }

        if (!$this->collRoles->contains($role)) { // only add it if the **same** object is not already associated
            $this->doAddRole($role);
            $this->collRoles[] = $role;

            if ($this->rolesScheduledForDeletion and $this->rolesScheduledForDeletion->contains($role)) {
                $this->rolesScheduledForDeletion->remove($this->rolesScheduledForDeletion->search($role));
            }
        }

        return $this;
    }

    /**
     * @param	Role $role The role object to add.
     */
    protected function doAddRole(Role $role)
    {
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$role->getUsers()->contains($this)) {
            $userRole = new UserRole();
            $userRole->setRole($role);
            $this->addUserRole($userRole);

            $foreignCollection = $role->getUsers();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a Role object to this object
     * through the UserRole cross reference table.
     *
     * @param Role $role The UserRole object to relate
     * @return User The current object (for fluent API support)
     */
    public function removeRole(Role $role)
    {
        if ($this->getRoles()->contains($role)) {
            $this->collRoles->remove($this->collRoles->search($role));
            if (null === $this->rolesScheduledForDeletion) {
                $this->rolesScheduledForDeletion = clone $this->collRoles;
                $this->rolesScheduledForDeletion->clear();
            }
            $this->rolesScheduledForDeletion[]= $role;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->uri = null;
        $this->username = null;
        $this->email = null;
        $this->password = null;
        $this->registered_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collPhotos) {
                foreach ($this->collPhotos as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPhotoComments) {
                foreach ($this->collPhotoComments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserRates) {
                foreach ($this->collUserRates as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->singleSettings) {
                $this->singleSettings->clearAllReferences($deep);
            }
            if ($this->collUserRoles) {
                foreach ($this->collUserRoles as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRatings) {
                foreach ($this->collRatings as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRoles) {
                foreach ($this->collRoles as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collPhotos instanceof PropelCollection) {
            $this->collPhotos->clearIterator();
        }
        $this->collPhotos = null;
        if ($this->collPhotoComments instanceof PropelCollection) {
            $this->collPhotoComments->clearIterator();
        }
        $this->collPhotoComments = null;
        if ($this->collUserRates instanceof PropelCollection) {
            $this->collUserRates->clearIterator();
        }
        $this->collUserRates = null;
        if ($this->singleSettings instanceof PropelCollection) {
            $this->singleSettings->clearIterator();
        }
        $this->singleSettings = null;
        if ($this->collUserRoles instanceof PropelCollection) {
            $this->collUserRoles->clearIterator();
        }
        $this->collUserRoles = null;
        if ($this->collRatings instanceof PropelCollection) {
            $this->collRatings->clearIterator();
        }
        $this->collRatings = null;
        if ($this->collRoles instanceof PropelCollection) {
            $this->collRoles->clearIterator();
        }
        $this->collRoles = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

    // sluggable behavior

    /**
     * Wrap the setter for slug value
     *
     * @param   string
     * @return  User
     */
    public function setSlug($v)
    {
        return $this->setUri($v);
    }

    /**
     * Wrap the getter for slug value
     *
     * @return  string
     */
    public function getSlug()
    {
        return $this->getUri();
    }

    /**
     * Create a unique slug based on the object
     *
     * @return string The object slug
     */
    protected function createSlug()
    {
        $slug = $this->createRawSlug();
        $slug = $this->limitSlugSize($slug);
        $slug = $this->makeSlugUnique($slug);

        return $slug;
    }

    /**
     * Create the slug from the appropriate columns
     *
     * @return string
     */
    protected function createRawSlug()
    {
        return '' . $this->cleanupSlugPart($this->getusername()) . '';
    }

    /**
     * Cleanup a string to make a slug of it
     * Removes special characters, replaces blanks with a separator, and trim it
     *
     * @param     string $slug        the text to slugify
     * @param     string $replacement the separator used by slug
     * @return    string               the slugified text
     */
    protected static function cleanupSlugPart($slug, $replacement = '-')
    {
        // transliterate
        if (function_exists('iconv')) {
            $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        }

        // lowercase
        if (function_exists('mb_strtolower')) {
            $slug = mb_strtolower($slug);
        } else {
            $slug = strtolower($slug);
        }

        // remove accents resulting from OSX's iconv
        $slug = str_replace(array('\'', '`', '^'), '', $slug);

        // replace non letter or digits with separator
        $slug = preg_replace('/[^\w\/]+/u', $replacement, $slug);

        // trim
        $slug = trim($slug, $replacement);

        if (empty($slug)) {
            return 'n-a';
        }

        return $slug;
    }


    /**
     * Make sure the slug is short enough to accommodate the column size
     *
     * @param    string $slug                   the slug to check
     * @param    int    $incrementReservedSpace the number of characters to keep empty
     *
     * @return string                            the truncated slug
     */
    protected static function limitSlugSize($slug, $incrementReservedSpace = 3)
    {
        // check length, as suffix could put it over maximum
        if (strlen($slug) > (250 - $incrementReservedSpace)) {
            $slug = substr($slug, 0, 250 - $incrementReservedSpace);
        }

        return $slug;
    }


    /**
     * Get the slug, ensuring its uniqueness
     *
     * @param    string $slug            the slug to check
     * @param    string $separator       the separator used by slug
     * @param    int    $alreadyExists   false for the first try, true for the second, and take the high count + 1
     * @return   string                   the unique slug
     */
    protected function makeSlugUnique($slug, $separator = '_', $alreadyExists = false)
    {
        if (!$alreadyExists) {
            $slug2 = $slug;
        } else {
            $slug2 = $slug . $separator;
        }

         $query = UserQuery::create('q')
        ->where('q.Uri ' . ($alreadyExists ? 'REGEXP' : '=') . ' ?', $alreadyExists ? '^' . $slug2 . '[0-9]+$' : $slug2)->prune($this)
        ;

        if (!$alreadyExists) {
            $count = $query->count();
            if ($count > 0) {
                return $this->makeSlugUnique($slug, $separator, true);
            }

            return $slug2;
        }

        // Already exists
        $object = $query
            ->addDescendingOrderByColumn('LENGTH(uri)')
            ->addDescendingOrderByColumn('uri')
        ->findOne();

        // First duplicate slug
        if (null == $object) {
            return $slug2 . '1';
        }

        $slugNum = substr($object->getUri(), strlen($slug) + 1);
        if ('0' === $slugNum[0]) {
            $slugNum[0] = 1;
        }

        return $slug2 . ($slugNum + 1);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     User The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = UserPeer::UPDATED_AT;

        return $this;
    }

}
