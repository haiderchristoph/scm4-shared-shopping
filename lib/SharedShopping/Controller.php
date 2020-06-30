<?php

namespace SharedShopping;
use SharedShopping\UserType;
use SharedShopping\ShoppingListStatus;
use SharedShopping\PermissionManager;
use SharedShopping\ValidationManager;
use \Logging\FileLogger;

class Controller extends BaseObject
{
    const PAGE = 'page';
    const ACTION = 'action';
    const SL_ID = 'shoppingListId';
    const SL_TITLE = 'listTitle';
    const SL_DATE_UNTIL = 'listDateUntil';
    const SL_TOTAL_PRICE = 'totalPrice';
    const ARTICLE_ID = 'articleId';
    const ARTICLE_TITLE = 'articleTitle';
    const ARTICLE_AMOUNT = 'articleAmount';
    const ARTICLE_PRICE = 'articlePrice';
    const ACTION_SL_CREATE = 'createListAction';
    const ACTION_SL_UPDATE = 'updateListAction';
    const ACTION_SL_DELETE = 'deleteListAction';
    const ACTION_SL_PUBLISH = 'publishListAction';
    const ACTION_SL_INPROGRESS = 'inprogressListAction';
    const ACTION_SL_DONE = 'doneListAction';
    const ACTION_ARTICLE_CREATE = 'createArticleAction';
    const ACTION_ARTICLE_MODIFY = 'modifyArticleAction';
    const ACTION_ARTICLE_UPDATE = 'updateArticleAction';
    const ACTION_ARTICLE_DELETE = 'deleteArticleAction';
    const ACTION_ARTICLE_DONE = 'doneArticleAction';
    const ACTION_ARTICLE_NOT_DONE = 'notDoneArticleAction';
    const ACTION_LOGIN = 'loginAction';
    const ACTION_LOGOUT = 'logoutAction';
    const USER_NAME = 'userName';
    const USER_PASSWORD = 'password';
    const USER_ID = 'user';

    private static $instance = false;
    private static $logger = null;
    

    public static function getInstance() : Controller {
        if (!self::$instance) {
            self::$instance = new Controller();
        }
        return self::$instance;
    }

    private function __construct() {
        self::$logger = new FileLogger(__DIR__ . '/../../logs/log.log');
    }

    public function invokePostAction () : bool {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            throw new \Exception('Controller can only handle Post Requests');
            return null;
        }
        elseif (!isset($_REQUEST[self::ACTION])) {
            throw new \Exception(self::ACTION . ' not specified.');
            return null;
        }

        $action = $_REQUEST[self::ACTION];


        switch ($action) {
            case self::ACTION_SL_CREATE :
                if (PermissionManager::canCreateShoppingList()) {
                    $id = $this->processCreateList($_SESSION[self::USER_ID], $_POST[self::SL_TITLE], $_POST[self::SL_DATE_UNTIL]);
                    if ($id == -1) {
                        self::$logger->log('Create Shopping list failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_TITLE: ' . $_POST[self::SL_TITLE] . ' | SL_DATE_UNTIL: ' . $_POST[self::SL_DATE_UNTIL], FileLogger::ERROR);
                        $this->forwardRequest(['Create Shopping list failed']);
                    }
                    self::$logger->log('Created Shopping - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_TITLE: ' . $_POST[self::SL_TITLE] . ' | SL_DATE_UNTIL: ' . $_POST[self::SL_DATE_UNTIL], FileLogger::NOTICE);
                    Util::redirect('index.php?view=detail&shoppingListId=' . $id);
                }
                self::$logger->log('No permission to create shopping list. - user id: ' . $_SESSION[self::USER_ID], FileLogger::ERROR);
                $this->forwardRequest(['No permission to create shopping list.']);
                break;
            
            case self::ACTION_SL_UPDATE :
                if (PermissionManager::canUpdateShoppingList($_REQUEST[self::SL_ID])) {
                    if (!$this->processUpdateList($_REQUEST[self::SL_ID], $_POST[self::SL_TITLE], $_POST[self::SL_DATE_UNTIL])) {
                        self::$logger->log('Update Shopping list failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_TITLE: ' . $_POST[self::SL_TITLE] . ' | SL_DATE_UNTIL: ' . $_POST[self::SL_DATE_UNTIL], FileLogger::ERROR);
                        $this->forwardRequest(['Update shopping list failed']);
                    }
                    self::$logger->log('Updated Shopping - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_TITLE: ' . $_POST[self::SL_TITLE] . ' | SL_DATE_UNTIL: ' . $_POST[self::SL_DATE_UNTIL], FileLogger::NOTICE);
                    Util::redirect();
                }
                self::$logger->log('No permission to update shopping list. - user id: ' . $_SESSION[self::USER_ID], FileLogger::ERROR);
                $this->forwardRequest(['No permission to update shopping list.']);
                break;

            case self::ACTION_SL_PUBLISH :
                if (PermissionManager::canUpdateShoppingListToOpen($_REQUEST[self::SL_ID])) {
                    if (!$this->processUpdateListStatus($_REQUEST[self::SL_ID], ShoppingListStatus::OPEN, null)) {
                        self::$logger->log('Change state of Shopping list to OPEN failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID], FileLogger::ERROR);
                        $this->forwardRequest(['Changing state of shopping list failed']);
                    }
                    self::$logger->log('Changed state of shopping list to OPEN - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID], FileLogger::NOTICE);
                    Util::redirect();
                }
                self::$logger->log('No permission to change state of shopping list. - user id: ' . $_SESSION[self::USER_ID], FileLogger::ERROR);
                $this->forwardRequest(['No permission to change state of shopping list']);
                
                break;

            case self::ACTION_SL_INPROGRESS :
                if (PermissionManager::canUpdateShoppingListToInProgress($_REQUEST[self::SL_ID])) {
                    if (!$this->processUpdateListStatus($_REQUEST[self::SL_ID], ShoppingListStatus::IN_PROGRESS, $_SESSION[self::USER_ID])) {
                        self::$logger->log('Change state of Shopping list to IN_PROGRESS failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID], FileLogger::ERROR);
                        $this->forwardRequest(['Changing state of shopping list failed']);
                    }
                    self::$logger->log('Changed state of shopping list to INPROGRESS - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID], FileLogger::NOTICE);
                    Util::redirect();
                }
                self::$logger->log('No permission to change state of shopping list. - user id: ' . $_SESSION[self::USER_ID], FileLogger::ERROR);
                $this->forwardRequest(['No permission to change state of shopping list']);
                break;

            case self::ACTION_SL_DONE :
                if (PermissionManager::canUpdateShoppingListToDone($_REQUEST[self::SL_ID])) {
                    $allValid = ValidationManager::validateFloat($_POST[self::SL_TOTAL_PRICE]);
                    if ($allValid) {
                        if (!$this->processListDoneAndTotalPrice($_REQUEST[self::SL_ID], $_POST[self::SL_TOTAL_PRICE])) {
                            self::$logger->log('Change state of Shopping list to DONE failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID] . ' | SL_TOTAL_PRICE: ' . $_POST[self::SL_TOTAL_PRICE], FileLogger::ERROR);
                            $this->forwardRequest(['Changing state of shopping list failed']);
                        }
                        self::$logger->log('Changed state of shopping list to DONE - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID], FileLogger::NOTICE);
                        Util::redirect();
                    }
                    $this->forwardRequest(['Given input data not valid']);
                }
                self::$logger->log('No permission to change state of shopping list. - user id: ' . $_SESSION[self::USER_ID], FileLogger::ERROR);
                $this->forwardRequest(['No permission to change state of shopping list']);
                break;

            case self::ACTION_SL_DELETE :
                if (PermissionManager::canDeleteShoppingList($_REQUEST[self::SL_ID])) {
                    if (!$this->processDeleteList($_REQUEST[self::SL_ID])) {
                        self::$logger->log('Deletion of shopping list failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID], FileLogger::ERROR);
                        $this->forwardRequest(['Deletion of shopping list failed']);
                    }
                    self::$logger->log('Deletion of shopping list successful - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID], FileLogger::NOTICE);
                    Util::redirect('index.php?view=dashboard');
                }
                self::$logger->log('No permission to delete this shopping list. - user id: ' . $_SESSION[self::USER_ID], FileLogger::ERROR);
                $this->forwardRequest(['No permission to delete this shopping list']);
                break;

            case self::ACTION_ARTICLE_CREATE :
                if (PermissionManager::canCreateOrManipulateArticle($_REQUEST[self::SL_ID])) {
                    $allValid = (
                        ValidationManager::validateInteger($_POST[self::ARTICLE_AMOUNT])
                        && ValidationManager::validateFloat($_POST[self::ARTICLE_PRICE])
                    );
                    if ($allValid) {
                        if (!$this->processCreateArticle($_REQUEST[self::SL_ID], $_POST[self::ARTICLE_TITLE], $_POST[self::ARTICLE_AMOUNT], $_POST[self::ARTICLE_PRICE])) {
                            self::$logger->log('Create article failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID] . ' | ARTICLE_TITLE: ' . $_POST[self::ARTICLE_TITLE] . ' | ARTICLE_AMOUNT: ' . $_POST[self::ARTICLE_AMOUNT] . ' | ARTICLE_PRICE: ' . $_POST[self::ARTICLE_PRICE], FileLogger::ERROR);
                            $this->forwardRequest(['Create article failed']);
                        }
                        self::$logger->log('Created article - user id: ' . $_SESSION[self::USER_ID] . ' | data: SL_ID: ' . $_REQUEST[self::SL_ID], FileLogger::NOTICE);
                        Util::redirect('index.php?view=detail&shoppingListId=' . $_REQUEST[self::SL_ID]);
                    }
                    $this->forwardRequest(['Given input data not valid']);
                }
                self::$logger->log('No permission to create article. - user id: ' . $_SESSION[self::USER_ID], FileLogger::ERROR);
                $this->forwardRequest(['No permission to create article']);
                break;

            case self::ACTION_ARTICLE_MODIFY :
                
                    if (isset($_POST[self::ACTION_ARTICLE_UPDATE])) {
                        if (PermissionManager::canCreateOrManipulateArticle($_REQUEST[self::SL_ID])) {
                            if (!$this->processUpdateArticle($_REQUEST[self::ARTICLE_ID], $_REQUEST[self::ARTICLE_TITLE], $_REQUEST[self::ARTICLE_AMOUNT], $_REQUEST[self::ARTICLE_PRICE])) {
                                self::$logger->log('Update article failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID] . ' | ARTICLE_TITLE: ' . $_POST[self::ARTICLE_TITLE] . ' | ARTICLE_AMOUNT: ' . $_POST[self::ARTICLE_AMOUNT] . ' | ARTICLE_PRICE: ' . $_POST[self::ARTICLE_PRICE], FileLogger::ERROR);
                                $this->forwardRequest(['Update article failed']);
                            }
                            self::$logger->log('Updated article - user id: ' . $_SESSION[self::USER_ID] . ' | data: ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::NOTICE);
                            Util::redirect('index.php?view=detail&shoppingListId=' . $_REQUEST[self::SL_ID]);
                        }
                        self::$logger->log('No permission to update article. - user id: ' . $_SESSION[self::USER_ID] . ' | ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::ERROR);
                        $this->forwardRequest(['No permission to update article']);
                    } elseif (isset($_POST[self::ACTION_ARTICLE_DELETE])) {
                        if (PermissionManager::canCreateOrManipulateArticle($_REQUEST[self::SL_ID])) {
                            if (!$this->processDeleteArticle($_REQUEST[self::ARTICLE_ID])) {
                                self::$logger->log('Delete article failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::ERROR);
                                $this->forwardRequest(['Delete article failed']);
                            }
                            self::$logger->log('Deleted article - user id: ' . $_SESSION[self::USER_ID] . ' | data: ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::NOTICE);
                            Util::redirect('index.php?view=detail&shoppingListId=' . $_REQUEST[self::SL_ID]);
                        }
                        self::$logger->log('No permission to delete article. - user id: ' . $_SESSION[self::USER_ID] . ' | ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::ERROR);
                        $this->forwardRequest(['No permission to delete article']);
                    } elseif (isset($_POST[self::ACTION_ARTICLE_DONE])) {
                        if (PermissionManager::canSetArticleToDone($_REQUEST[self::SL_ID], $_REQUEST[self::ARTICLE_ID])) {
                            if (!$this->processSetArticleDoneStatus($_REQUEST[self::SL_ID], $_REQUEST[self::ARTICLE_ID], true)) {
                                self::$logger->log('Update article to DONE failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::ERROR);
                                $this->forwardRequest(['Update article failed']);
                            }
                            self::$logger->log('Updated article to DONE - user id: ' . $_SESSION[self::USER_ID] . ' | data: ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::NOTICE);
                            Util::redirect('index.php?view=detail&shoppingListId=' . $_REQUEST[self::SL_ID]);
                        }
                        self::$logger->log('No permission to update article. - user id: ' . $_SESSION[self::USER_ID] . ' | ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::ERROR);
                        $this->forwardRequest(['No permission to update article']);
                    } elseif (isset($_POST[self::ACTION_ARTICLE_NOT_DONE])) {
                        if (PermissionManager::canSetArticleToDone($_REQUEST[self::SL_ID], $_REQUEST[self::ARTICLE_ID])) {
                            if (!$this->processSetArticleDoneStatus($_REQUEST[self::SL_ID], $_REQUEST[self::ARTICLE_ID], false)) {
                                self::$logger->log('Update article to NOT DONE failed - user id: ' . $_SESSION[self::USER_ID] . ' | data: ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::ERROR);
                                $this->forwardRequest(['Update article failed']);
                            }
                            self::$logger->log('Updated article to NOT DONE - user id: ' . $_SESSION[self::USER_ID] . ' | data: ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::NOTICE);
                            Util::redirect('index.php?view=detail&shoppingListId=' . $_REQUEST[self::SL_ID]);
                        }
                        self::$logger->log('No permission to update article. - user id: ' . $_SESSION[self::USER_ID] . ' | ARTICLE_ID: ' . $_REQUEST[self::ARTICLE_ID], FileLogger::ERROR);
                        $this->forwardRequest(['No permission to update article']);
                    }
                break;
            
            case self::ACTION_LOGIN :
                if (!AuthenticationManager::authenticate($_REQUEST[self::USER_NAME], $_REQUEST[self::USER_PASSWORD])) {
                    $this->forwardRequest(array('Invalid user name or password.'));
                }
                Util::redirect('index.php?view=welcome');
                break;

            case self::ACTION_LOGOUT :
                //sign out current user
                AuthenticationManager::signOut();
                Util::redirect();
                break;

            default :
                throw new \Exception('Unknown controller action: ' . $action);
                return null;
                break;
        }
    }

    /**
     *
     * @param array $errors : optional assign it to
     * @param string $target : url for redirect of the request
     */
    protected function forwardRequest(array $errors = null, $target = null) {
        //check for given target and try to fall back to previous page if needed
        if ($target == null) {
            if (!isset($_REQUEST[self::PAGE])) {
                throw new Exception('Missing target for forward.');
            }
            $target = $_REQUEST[self::PAGE];
        }
        //forward request to target
        // optional - add errors to redirect and process them in view
        if (count($errors) > 0)
            $target .= '&errors=' . urlencode(serialize($errors));
        header('location: ' . $target);
        exit();
    }

    protected function processCreateList(int $userId, string $listTitle, string $dateUntil) : int {
        $listId = \Data\DataManager::createList($userId, $listTitle, $dateUntil);
        if (!$listId) {
            $this->forwardRequest(['Could not create list']);
            return -1;
        }
        return $listId;
    }

    protected function processUpdateList(int $shoppingListId, string $title, string $dateUntil) : bool {
        $success = \Data\DataManager::updateShoppingList($shoppingListId, $title, $dateUntil);
        
        if (!$success) {
            $this->forwardRequest(['Could not update list']);
            return false;
        }
        return true;
    }

    protected function processUpdateListStatus(int $shoppingListId, string $status, ?int $volunteerId) : bool {
        $success = \Data\DataManager::updateShoppingListStatus($shoppingListId, $status, $volunteerId);
        
        if (!$success) {
            $this->forwardRequest(['Could not update list']);
            return false;
        }
        return true;
    }

    protected function processListDoneAndTotalPrice(int $shoppingListId, float $totalPrice) : bool {
        $success = \Data\DataManager::updateShoppingListTotalPrice($shoppingListId, $totalPrice);
        
        if (!$success) {
            $this->forwardRequest(['Could not update list']);
            return false;
        }
        return true;
    }

    protected function processDeleteList(int $shoppingListId) : bool {
        $success = \Data\DataManager::deleteShoppingList($shoppingListId);
        
        if (!$success) {
            $this->forwardRequest(['Could not delete list']);
            return false;
        }
        return true;
    }

    protected function processCreateArticle(int $shoppingListId, string $articleTitle, int $articleAmount, float $articlePrice) : bool {
        $articleId = \Data\DataManager::createArticle($shoppingListId, $articleTitle, $articlePrice, $articleAmount);
        
        if (!$articleId) {
            $this->forwardRequest(['Could not create article']);
            return false;
        }
        return true;
    }

    protected function processUpdateArticle(int $articleId, string $articleTitle, int $articleAmount, float $articlePrice) : bool {
        $success = \Data\DataManager::updateArticle($articleId, $articleTitle, $articlePrice, $articleAmount);
        
        if (!$success) {
            $this->forwardRequest(['Could not update article']);
            return false;
        }
        return true;
    }

    protected function processSetArticleDoneStatus(int $shoppingListId, int $articleId, bool $isDone) : bool {
        $success = \Data\DataManager::setArticleDoneStatus($shoppingListId, $articleId, $isDone);
        
        if (!$success) {
            $this->forwardRequest(['Could not update article']);
            return false;
        }
        return true;
    }

    protected function processDeleteArticle(int $articleId) : bool {
        $success = \Data\DataManager::deleteArticle($articleId);
        
        if (!$success) {
            $this->forwardRequest(['Could not delete article']);
            return false;
        }
        return true;
    }
}


?>