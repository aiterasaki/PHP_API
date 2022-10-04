<?php

/**
 * テーブルにデータを書き込み
 *
 * @param Object $db
 * @param String $name
 * @param String $memo
 * @return Boolean
 */
function insertTestTb($db, $name, $memo)
{
    // // json -> object　変換
    $params = json_decode(file_get_contents('php://input'), true);
    $name = $params["name"];
    $memo = $params["memo"];
    error_log('name: '  .$name, 0);
    error_log('memo: ' .$memo, 0);

    $sql = 'insert into test_tb (name,memo) values (?, ?)';
    $stmt = $db->prepare($sql);
    $result = $stmt->execute(array( $name,$memo));

    echo $result;
}

/**
 * テーブルの結果を配列で取得
 *
 * @param Object $db
 * @return Array // SQL取得の結果
 */
function selectTestTb($db)
{
    //DBに接続済みでfetchAllで該当する全てのデータを配列として取得。
    $stmt = $db->query('SELECT * FROM test_tb');
    // 配列で結果を取得
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function deleteTestTb($db, $id)
{
    // SQL作成
    $stmt = $db->prepare("DELETE FROM test_tb WHERE id = :id");
    // 登録するデータをセット
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    // SQL実行
    $result = $stmt->execute();

    return $result;
}


function updateTestTb($db, $id, $name, $memo)
{
    // SQL作成
    $stmt = $db->prepare("update test_tb set name= :name,memo= :memo where id= :id");

    // 登録するデータをセット
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':memo', $memo);

    // SQL実行
    $result = $stmt->execute();

    return $result;
}

/**
 * メイン処理
 */

//DB接続
try {
    $db = new PDO('mysql:dbname=testdb;port=8889;host=localhost;charset=utf8', 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    error_log("接続OK！", 0);
} catch (PDOException $e) {
    error_log("DB接続エラー！", 0). $e->getMessage();
}


// POST：フォームからの投稿などのとき
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // json -> object　変換
    $params = json_decode(file_get_contents('php://input'), true);
    $name = $params["name"];
    $memo = $params["memo"];

    $result = insertTestTb($db, $name, $memo);
    //SQLのログをerror_logに追記する
    $result ? error_log("データの追加に成功しました", 0) : error_log("データの追加に失敗しました", 0);
}

// GET：リンクのクリックによる表示のリクエストなどのとき
if ($_SERVER["REQUEST_METHOD"] == "GET") {

     $result = selectTestTb($db);
    //SQLのログをerror_logに追記する
    if (!empty($result)) {
        //入力した値を logs/php_error_log ファイルに追記したいため、error_log()を使う
        error_log("データの取得に成功しました", 0);
        error_log(print_r($result, true), 0);
    } else {
        error_log("データの取得に失敗しました", 0);
    }
    // フロントへデータを渡す
    // このデータをjavascriptのfetchで取得する(GET)
    // json_encodeで配列をJSON形式に変換

    // return json_encode($result);
    echo json_encode($result);
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $params = json_decode(file_get_contents('php://input'), true);
    $id = $params["list_id"];
    $result = deleteTestTb($db, $id);
    $result ? error_log("データの削除に成功しました", 0) : error_log("データの削除に失敗しました", 0);
}

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $params = json_decode(file_get_contents('php://input'), true);
    $id = $params["list_id"];
    $name = $params["name"];
    $memo = $params["memo"];

    $result = updateTestTb($db, $id, $name, $memo);

    $result ? error_log("データの更新に成功しました", 0) : error_log("データの更新に失敗しました", 0);
}

$db = null;