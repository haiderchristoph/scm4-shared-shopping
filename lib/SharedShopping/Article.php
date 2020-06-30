<?php
namespace SharedShopping;
/**
 * Article
 * 
 * 
 * @extends Entity
 * @package    
 * @subpackage 
 * @author     John Doe <jd@fbi.gov>
 */
class Article extends Entity {

  private $shoppingListId;
  private $title;
  private $priceLimit;
  private $amount;
  private $isDone;

  public function __construct(int $id, int $shoppingListId, string $title, float $priceLimit, int $amount, bool $isDone) {
    parent::__construct($id);
    $this->title = $title;
    $this->shoppingListId = $shoppingListId;
    $this->priceLimit = $priceLimit;
    $this->amount = $amount;
    $this->isDone = $isDone;
  }

  /**
   * getter for the private parameter $shoppingListId
   *
   * @return int
   */
  public function getShoppingListId() : int {
    return $this->shoppingListId;
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
   * getter for the private parameter $price_limit
   *
   * @return float
   */
  public function getPriceLimit() : float {
    return $this->priceLimit;
  }

  /**
   * getter for the private parameter $amount
   *
   * @return int
   */
  public function getAmount() : int {
    return $this->amount;
  }

  /**
   * getter for the private parameter $isDone
   *
   * @return bool
   */
  public function getIsDone() : bool {
    return $this->isDone;
  }
}