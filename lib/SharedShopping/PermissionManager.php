<?php


namespace SharedShopping;
use Data\DataManager;
use SharedShopping\ShoppingListStatus;

class PermissionManager extends BaseObject
{
    const USER_ID = 'user';

    /**
     * Determines if logged in user is allowed to update Shopping List status to IN_PROGRESS
     * Also checks if the current status is correct for the destination status
     * @return bool
     */
    public static function canUpdateShoppingListToInProgress(int $shoppingListId) : bool {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        $shoppingList = DataManager::getShoppingListById($shoppingListId);
        if (
            $userType == UserType::VOLUNTEER
            && $shoppingList
            && $volunteerId == null
            && $shoppingList->getStatus() == ShoppingListStatus::OPEN
            ) {
            return true;
        }
        return false;
    }

    /**
     * Determines if logged in user is allowed to update Shopping List status to DONE
     * Also checks if the current status is correct for the destination status
     * @return bool
     */
    public static function canUpdateShoppingListToDone(int $shoppingListId) : bool {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        $shoppingList = DataManager::getShoppingListById($shoppingListId);
        $allDone = DataManager::allArticlesDone($shoppingListId);
        if (
            $userType == UserType::VOLUNTEER
            && $shoppingList
            && $shoppingList->getStatus() == ShoppingListStatus::IN_PROGRESS
            && $_SESSION[self::USER_ID] == $shoppingList->getVolunteerId()
            && $allDone
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determines if logged in user is allowed to update Shopping List status to OPEN
     * Also checks if the current status is correct for the destination status
     * @return bool
     */
    public static function canUpdateShoppingListToOpen(int $shoppingListId) : bool {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        $shoppingList = DataManager::getShoppingListById($shoppingListId);
        if (
            $userType == UserType::HELPSEEKER
            && $shoppingList
            && $shoppingList->getStatus() == ShoppingListStatus::DRAFT
            && $shoppingList->getVolunteerId() == null
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determines if logged in user is allowed to create a new shopping list
     * @return bool
     */
    public static function canCreateShoppingList() : bool {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        if (
            $userType == UserType::HELPSEEKER
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determines if logged in user is allowed to delete a given shopping list
     * @return bool
     */
    public static function canDeleteShoppingList(int $shoppingListId) : bool {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        $shoppingList = DataManager::getShoppingListById($shoppingListId);
        if (
            $userType == UserType::HELPSEEKER
            && $shoppingList
            && $shoppingList->getCreatorId() == $_SESSION[self::USER_ID]
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determines if logged in user is allowed to update data for a given shopping list
     * @return bool
     */
    public static function canUpdateShoppingList(int $shoppingListId) : bool {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        $shoppingList = DataManager::getShoppingListById($shoppingListId);
        if (
            $userType == UserType::HELPSEEKER
            && $shoppingList
            && $shoppingList->getCreatorId() == $_SESSION[self::USER_ID]
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determines if logged in user is allowed to create, update or delete a given article
     * @return bool
     */
    public static function canCreateOrManipulateArticle(int $shoppingListId) : bool {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        $shoppingList = DataManager::getShoppingListById($shoppingListId);
        if (
            $userType == UserType::HELPSEEKER
            && $shoppingList
            && $shoppingList->getCreatorId() == $_SESSION[self::USER_ID]
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determines if logged in user is allowed to check Article to Done
     * @return bool
     */
    public static function canSetArticleToDone(int $shoppingListId, $articleId) : bool {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        $shoppingList = DataManager::getShoppingListById($shoppingListId);
        $article = DataManager::getArticleById($articleId);
        if (
            $userType == UserType::VOLUNTEER
            && $shoppingList
            && $shoppingList->getVolunteerId() == $_SESSION[self::USER_ID]
            && $article->getShoppingListId() == $shoppingListId
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determines if logged in user is gets readonly and disabled input fields in views or not
     * @return bool
     */
    public static function canManipulateDataInView(int $shoppingListId) : bool {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        $shoppingList = DataManager::getShoppingListById($shoppingListId);
        $shoppingListStatus = $shoppingList->getStatus();
        if ($userType == UserType::HELPSEEKER) {
            if ($shoppingList && ($shoppingListStatus == ShoppingListStatus::DRAFT)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines if logged in user is allowed to toggle the Article status
     * @return bool
     */
    public static function canSetArticlesToDone(int $shoppingListId) {
        $userType = AuthenticationManager::getAuthenticatedUserType();
        $shoppingList = DataManager::getShoppingListById($shoppingListId);
        $shoppingListStatus = $shoppingList->getStatus();
        if ($userType == UserType::VOLUNTEER) {
            if ($shoppingList && ($shoppingListStatus == ShoppingListStatus::IN_PROGRESS) && $shoppingList->getVolunteerId() == $_SESSION['user']) {
                return true;
            }
        }
        return false;
    }
    
}