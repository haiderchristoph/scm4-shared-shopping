<?php

namespace SharedShopping;

/**
 * User
 * 
 * 
 * @extends Entity
 * @package    
 * @subpackage 
 * @author     John Doe <jd@fbi.gov>
 */
class User extends Entity {

  private $userName;
  private $passwordHash;
  private $userType;

  public function __construct(int $id, string $userName, string $passwordHash, string $userType) {
    parent::__construct($id);
    $this->userName = $userName;
    $this->passwordHash = $passwordHash;
    $this->userType = $userType;
  }

  /**
   * getter for the private parameter $userName
   *
   * @return string
   */
  public function getUserName() : string {
    return $this->userName;
  }

  /**
   * getter for the private parameter $passwordHash
   *
   * @return string
   */
  public function getPasswordHash() : string {
    return $this->passwordHash;
  }

   /**
   * getter for the private parameter $userType
   *
   * @return string
   */
  public function getUserType() : string {
    return $this->userType;
  }

}