<?php
namespace SharedShopping;
/**
 * ShoppingList
 * 
 * @extends Entity
 */
class ShoppingList extends Entity {

  private $creatorId;
  private $volunteerId;
  private $title;
  private $dateUntil;
  private $status;
  private $totalPrice;

  public function __construct(int $id, int $creatorId, ?int $volunteerId, string $title, string $dateUntil, string $status, ?float $totalPrice) {
    parent::__construct($id);
    $this->creatorId = $creatorId;
    $this->volunteerId = $volunteerId;
    $this->title = $title;
    $this->dateUntil = $dateUntil;
    $this->status = $status;
    $this->totalPrice = $totalPrice;
  }

  /**
   * getter for the private parameter $creatorId
   *
   * @return int
   */
  public function getCreatorId() : int {
    return $this->creatorId;
  }

  /**
   * getter for the private parameter $volunteerId
   *
   * @return int
   */
  public function getVolunteerId() : ?int {
    return $this->volunteerId;
  }

  /**
   * getter for the private parameter $title
   *
   * @return string
   */
  public function getTitle() : string {
    return $this->title;
  }

  /**
   * getter for the private parameter $dateUntil
   *
   * @return string
   */
  public function getDateUntil() : string {
    return $this->dateUntil;
  }

  /**
   * getter for the private parameter $status
   *
   * @return string
   */
  public function getStatus() : string {
    return $this->status;
  }

  /**
   * getter for the private parameter $totalPrice
   *
   * @return float
   */
  public function getTotalPrice() : ?float {
    return $this->totalPrice;
  }
}