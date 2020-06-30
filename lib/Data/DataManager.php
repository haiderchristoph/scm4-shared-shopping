<?php

namespace Data;
use SharedShopping\Article;
use SharedShopping\ShoppingList;
use SharedShopping\User;
use SharedShopping\ShoppingListStatus;

class DataManager
{   
    private static $__connection;

    private static function getConnection()
    {

        if (!isset(self::$__connection)) {

            $type = 'mysql';
            $host = 'localhost';
            $name = 'fh_2020_scm4_S1810307013';
            $user = 'fh_2020_scm4';
            $pass = 'fh_2020_scm4';

            self::$__connection = new \PDO($type . ':host=' . $host . ';dbname=' . $name . ';charset=utf8', $user, $pass);
        }
        return self::$__connection;
    }

    public static function exposeConnection()
    {
        return self::getConnection();
    }

    private static function query($connection, $query, $parameters = [])
    {
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        // preventing emulated prepared statements for sql injection
        $connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        try {
            $statement = $connection->prepare($query);
            $i = 1;
            foreach ($parameters as $param) {
                if (is_int($param)) {
                    $statement->bindValue($i, $param, \PDO::PARAM_INT);
                }
                if (is_string($param)) {
                    $statement->bindValue($i, $param, \PDO::PARAM_STR);
                }
                if (is_bool($param)) {
                    $statement->bindValue($i, $param, \PDO::PARAM_BOOL);
                }
                $i++;
            }
            $statement->execute();
        } catch (\Exception $e) {
            die ($e->getMessage());
        }
        return $statement;
    }

    private static function fetchObject($cursor)
    {
        return $cursor->fetch(\PDO::FETCH_OBJ);
    }

    private static function close($cursor)
    {
        $cursor->closeCursor();
    }

    private static function closeConnection()
    {
        self::$__connection = null;
    }

    private static function lastInsertId ($connection) {
        return $connection->lastInsertId();
    }

    /**
     * Get Articles by given shopping list ID
     *
     * @param int $shoppingListId ID of the shopping list
     * @return array of Article | null
     */
    public static function getArticlesByShoppingListId(int $shoppingListId): array
    {
        $articles = [];
        $con = self::getConnection();
        $res = self::query($con, "
            SELECT id, shopping_list_id, title, price_limit, amount, is_done 
            FROM articles 
            WHERE shopping_list_id = ? AND deleted_at IS NULL;
        ", [$shoppingListId]);
        while ($article = self::fetchObject($res)) {
            $articles[] = new Article($article->id, $article->shopping_list_id, $article->title, $article->price_limit, $article->amount, $article->is_done);
        }
        self::close($res);
        self::closeConnection();
        return $articles;
    }

    /**
     * Check if all articles of a given shopping list are done
     *
     * @param int $shoppingListId ID of the shopping list
     * @return Article | null
     */
    public static function allArticlesDone(int $shoppingListId): bool
    {
        $allDone = true;
        $con = self::getConnection();
        $res = self::query($con, "
            SELECT is_done 
            FROM articles 
            WHERE shopping_list_id = ? AND deleted_at IS NULL;
        ", [$shoppingListId]);
        while ($article = self::fetchObject($res)) {
            if (!$article->is_done) {
                $allDone = false;
            }
        }
        self::close($res);
        self::closeConnection();
        return $allDone;
    }

    /**
     * Get Article by given ID
     *
     * @param int $articleId ID of the article
     * @return Article | null
     */
    public static function getArticleById(int $articleId): Article
    {
        $con = self::getConnection();
        $res = self::query($con, "
            SELECT id, shopping_list_id, title, price_limit, amount, is_done 
            FROM articles 
            WHERE id = ? AND deleted_at IS NULL;
        ", [$articleId]);
        $articleRes = self::fetchObject($res);
        $article = new Article($articleRes->id, $articleRes->shopping_list_id, $articleRes->title, $articleRes->price_limit, $articleRes->amount, $articleRes->is_done);
        self::close($res);
        self::closeConnection();
        return $article;
    }

    /**
     * Get Shopping List by given ID
     *
     * @param int $shoppingListId ID of the shopping list
     * @return ShoppingList | null
     */
    public static function getShoppingListById(int $shoppingListId): ?ShoppingList
    {
        $dateFormatString = "Y-m-d";
        $con = self::getConnection();
        $res = self::query($con, "
            SELECT id, creator_id, volunteer_id, title, date_until, status, total_price 
            FROM shopping_list 
            WHERE id = ? AND deleted_at IS NULL;
        ", [$shoppingListId]);
        $resSL = self::fetchObject($res);
        $shoppingList = null;
        if ($resSL) {
            $shoppingList = new ShoppingList($resSL->id, $resSL->creator_id, $resSL->volunteer_id, $resSL->title, date($dateFormatString, strtotime($resSL->date_until)), $resSL->status, $resSL->total_price);
        }
        self::close($res);
        self::closeConnection();
        return $shoppingList;
    }

    /**
     * Get Shopping List by given Creator Id
     *
     * @param integer $creatorId ID of the creator
     * @return array of ShoppingList
     */
    public static function getShoppingListsByCreatorId(int $creatorId = null): array
    {   $shoppingLists = [];
        $dateFormatString = "Y-m-d";
        $con = self::getConnection();
        // only selects shoppingslists which are not yet deleted
        $res = self::query($con, "
            SELECT id, creator_id, volunteer_id, title, date_until, status, total_price 
            FROM shopping_list 
            WHERE creator_id = ? AND deleted_at IS NULL;
        ", [$creatorId]);
        while ($list = self::fetchObject($res)) {
            $shoppingLists[] = new ShoppingList($list->id, $list->creator_id, $list->volunteer_id, $list->title, date($dateFormatString, strtotime($list->date_until)), $list->status, $list->total_price);
        }
        self::close($res);
        self::closeConnection();
        return $shoppingLists;
    }

    /**
     * Get Shopping List by given Volunteer Id
     *
     * @param integer $volunteerId ID of the volunteer
     * @return array of ShoppingList
     */
    public static function getShoppingListsByVolunteerId(int $volunteerId = null): array
    {   $shoppingLists = [];
        $dateFormatString = "Y-m-d";
        $con = self::getConnection();
        // only selects shoppingslists which are not yet deleted
        $res = self::query($con, "
            SELECT id, creator_id, volunteer_id, title, date_until, status, total_price 
            FROM shopping_list 
            WHERE volunteer_id = ? AND deleted_at IS NULL;
        ", [$volunteerId]);
        while ($list = self::fetchObject($res)) {
            $shoppingLists[] = new ShoppingList($list->id, $list->creator_id, $list->volunteer_id, $list->title, date($dateFormatString, strtotime($list->date_until)), $list->status, $list->total_price);
        }
        self::close($res);
        self::closeConnection();
        return $shoppingLists;
    }

    /**
     * Get all open shopping list
     *
     * @return array of ShoppingList
     */
    public static function getOpenShoppingLists(): array
    {   $shoppingLists = [];
        $dateFormatString = "Y-m-d";
        $con = self::getConnection();
        // only selects shoppingslists which are not yet deleted
        $res = self::query($con, "
            SELECT id, creator_id, volunteer_id, title, date_until, status, total_price 
            FROM shopping_list 
            WHERE volunteer_id IS NULL AND deleted_at IS NULL AND status = 'Open';
        ", []);
        while ($list = self::fetchObject($res)) {
            $shoppingLists[] = new ShoppingList($list->id, $list->creator_id, $list->volunteer_id, $list->title, date($dateFormatString, strtotime($list->date_until)), $list->status, $list->total_price);
        }
        self::close($res);
        self::closeConnection();
        return $shoppingLists;
    }

    /**
     * get the User item by id
     *
     * @param integer $userId uid of that user
     * @return User | null
     */
    public static function getUserById(int $userId)
    { // no return type, cos "null" is not a valid User
        $user = null;
        $con = self::getConnection();
        $res = self::query($con, " 
            SELECT id, user_name, password_hash, user_type
            FROM users 
            WHERE id = ?;
        ", [$userId]);
        if ($u = self::fetchObject($res)) {
            $user = new User($u->id, $u->user_name, $u->password_hash, $u->user_type);
        }
        self::close($res);
        self::closeConnection($con);
        return $user;
    }

    /**
     * Gets the User by name
     *
     * @param string $userName name of that user - must be exact match
     * @return User | null
     */
    public static function getUserByUserName(string $userName)
    {

        $user = null;
        $con = self::getConnection();
        $res = self::query($con, " 
            SELECT id, user_name, password_hash, user_type
            FROM users 
            WHERE user_name = ?;
        ", [$userName]);
        if ($u = self::fetchObject($res)) {
            $user = new User($u->id, $u->user_name, $u->password_hash, $u->user_type);
        }
        self::close($res);
        self::closeConnection($con);
        return $user;
    }

    /**
     * Creates new article and returns generated id
     *
     * @param int $shoppingListId ID of shopping list the article is added to
     * @param string $title Value of title to be set
     * @param float $priceLimit Value of price limit until to be set
     * @param int $amount Value of amount to be set
     * @return int
     */
    public static function createArticle(int $shoppingListId, string $title, float $priceLimit, int $amount) : int {
        $con = self::getConnection();
        $con->beginTransaction();
        
        try {
            self::query ($con, "
                INSERT INTO articles (
                    shopping_list_id,
                    title,
                    price_limit,
                    amount
                ) VALUES (
                    ?, ?, ?, ?
                );
            ", [$shoppingListId, $title, number_format($priceLimit, 2, '.', ''), $amount]);
            $articleId = self::lastInsertId($con);
            $con->commit();
        }
        catch (\Exception $e) {
            
            print_r($e);
            $con->rollBack();
            $articleId = null;
        }
        self::closeConnection();
        return $articleId;
    }

    /**
     * Creates new list and returns generated id
     *
     * @param int $userId ID of the creator of the list
     * @param string $title Value of Title to be set
     * @param string $dateUntil Value of date until to be set
     * @return int
     */
    public static function createList(int $userId, string $title, string $dateUntil) : int {
        $con = self::getConnection();
        $con->beginTransaction();
        $dateFormatString = "Y-m-d";

        try {
            
            self::query ($con, "
                INSERT INTO shopping_list ( 
                    creator_id, 
                    title, 
                    date_until,
                    created_at,
                    status
                ) VALUES (
                    ?, ?, ?, ?, ?
                );
            ", [$userId, $title, date($dateFormatString, strtotime($dateUntil)), date($dateFormatString), ShoppingListStatus::DRAFT]);

            $shoppingListId = self::lastInsertId($con);
            $con->commit();
        }
        catch (\Exception $e) {
            $con->rollBack();
            $shoppingListId = null;
        }
        self::closeConnection();
        return $shoppingListId;
    }

    /**
     * Updates Article data
     *
     * @param int $articleId ID of Article to be updated
     * @param string $title Value of Title to be set
     * @param float $priceLimit Value of price limit to be set
     * @param int $amount   Value of amount to be set
     * @return bool
     */
    public static function updateArticle(int $articleId, string $title, float $priceLimit, int $amount) : bool {
        $con = self::getConnection();
        $con->beginTransaction();
        
        try {
            self::query ($con, "
                UPDATE articles
                SET title = ?, price_limit = ?, amount = ?
                WHERE id = ?;
            ", [$title, number_format($priceLimit, 2, '.', ''), $amount, $articleId]);
            $con->commit();
        }
        catch (\Exception $e) {
            
            print_r($e);
            $con->rollBack();
            return false;
        }
        self::closeConnection();
        return true;
    }

    /**
     * Updates Article Done Status
     *
     * @param int $shoppingListId Referenced Shopping List ID of article
     * @param int $articleId Article ID to be updated
     * @param bool $isDone true for done, or false for not done
     * @return bool
     */
    public static function setArticleDoneStatus(int $shoppingListId, int $articleId, bool $isDone) : bool {
        $con = self::getConnection();
        $con->beginTransaction();
        // boolean does not work with the pdo statement so just map it to a TINYINT value
        if ($isDone) {
            $isDone = 1;
        } else {
            $isDone = 0;
        }
        try {
            $query = "UPDATE articles SET is_done = ? WHERE id = ? AND shopping_list_id = ?";
            $con->prepare($query)->execute([$isDone, $articleId, $shoppingListId]);
            $con->commit();
        }
        catch (\Exception $e) {
            print_r($e);
            $con->rollBack();
            return false;
        }
        self::closeConnection();
        return true;
    }

    /**
     * Updates Shopping List data like title and date until
     *
     * @param int $id Shopping List ID to be updated
     * @param string $title title of the new shopping list
     * @param string $dateUntil date until of the new shopping list
     * @return bool
     */
    public static function updateShoppingList(int $id, string $title, string $dateUntil) : bool {
        $con = self::getConnection();
        $con->beginTransaction();
        $dateFormatString = "Y-m-d H:i:s";
        
        try {
            self::query ($con, "
                UPDATE shopping_list
                SET title = ?, date_until = ?
                WHERE id = ?;
            ", [$title, date($dateFormatString, strtotime($dateUntil)), $id]);
            $con->commit();
        }
        catch (\Exception $e) {
            
            print_r($e);
            $con->rollBack();
            return false;
        }
        self::closeConnection();
        return true;
    }

    /**
     * Updates Shopping List status, optionally also the volunteer
     *
     * @param int $id Shopping List ID to be updated
     * @param string $status shopping list status to be set
     * @param ?int $volunteerId volunteer id to be set
     * @return bool
     */
    public static function updateShoppingListStatus(int $id, string $status, ?int $volunteerId = null) : bool {
        $con = self::getConnection();
        $con->beginTransaction();
        try {
            $query = "UPDATE shopping_list SET status = ?, volunteer_id = ? WHERE id = ?";
            $con->prepare($query)->execute([$status, $volunteerId, $id]);
            $con->commit();
        }
        catch (\Exception $e) {
            
            print_r($e);
            $con->rollBack();
            return false;
        }
        self::closeConnection();
        return true;
    }

    /**
     * updates the total price and status of a shopping list with given id
     *
     * @param int $id Shopping List ID to be updated
     * @param float $totalPrice Total price that was paid
     * @return bool
     */
    public static function updateShoppingListTotalPrice(int $id, float $totalPrice) : bool {
        $con = self::getConnection();
        $con->beginTransaction();
        try {
            $query = "UPDATE shopping_list SET status = 'Done', total_price = ? WHERE id = ?";
            $con->prepare($query)->execute([$totalPrice, $id]);
            $con->commit();
        }
        catch (\Exception $e) {
            
            print_r($e);
            $con->rollBack();
            return false;
        }
        self::closeConnection();
        return true;
    }

    /**
     * marks article with given ID as deleted.
     *
     * @param int $articleId Article ID to be deleted
     * @return bool
     */
    public static function deleteArticle(int $articleId) : bool {
        $con = self::getConnection();
        $con->beginTransaction();
        $dateFormatString = "Y-m-d H:i:s";
        // actually just mark as deleted
        try {
            self::query ($con, "
                UPDATE articles
                SET deleted_at = ?
                WHERE id = ?;
            ", [date($dateFormatString), $articleId]);
            $con->commit();
        }
        catch (\Exception $e) {
            print_r($e);
            $con->rollBack();
            return false;
        }
        self::closeConnection();
        return true;
    }

    /**
     * marks shopping list with given ID as deleted.
     *
     * @param int $id Shopping List ID to be deleted
     * @return bool
     */
    public static function deleteShoppingList(int $id) : bool {
        $con = self::getConnection();
        $con->beginTransaction();
        $dateFormatString = "Y-m-d H:i:s";
        // actually just mark as deleted
        try {
            self::query ($con, "
                UPDATE shopping_list 
                SET deleted_at = ? 
                WHERE id = ?;
            ", [date($dateFormatString), $id]);
            $con->commit();
        }
        catch (\Exception $e) {
            
            print_r($e);
            $con->rollBack();
            return false;
        }
        
        self::closeConnection();

        $con = self::getConnection();
        $con->beginTransaction();
        try {
            self::query ($con, "
                UPDATE articles 
                SET deleted_at = ? 
                WHERE shopping_list_id = ?;
            ", [date($dateFormatString), $id]);
            $con->commit();
        }
        catch (\Exception $e) {
            
            print_r($e);
            $con->rollBack();
            return false;
        }
        self::closeConnection();
        return true;
    }
}

?>