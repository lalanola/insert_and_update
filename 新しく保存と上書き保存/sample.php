<?php
session_start();
define("COLUMN_NUMBER", 3);
require_once("db.php");
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_SESSION["token"]) && $_SESSION["token"] == $_POST["token"]) {

        try {
            $pdo = db();
            $insert_placeholder = "";
            $update_sql = array();
            $update_add_delete = array();
            $update_add_delete_placeholder = "";
            $insert_column = array();
            foreach ($_POST["insert_title"] as $key => $value) {
                if (!$_POST["insert_title"][$key] && $_POST["insert_checkbox1"][$key] == "0" && $_POST["insert_checkbox2"][$key] == "0") {
                    continue;
                }
                $insert_placeholder .= "(?,?,?),";
            }
            if ($insert_placeholder) {
                $insert_placeholder = substr($insert_placeholder, 0, -1);
                $stmt = $pdo->prepare("INSERT INTO list (check1,check2,`name`) VALUES $insert_placeholder");
                $i = 0;
                foreach ($_POST["insert_title"] as $key => $value) {
                    if (!$_POST["insert_title"][$key] && $_POST["insert_checkbox1"][$key] == "0" && $_POST["insert_checkbox2"][$key] == "0") {
                        continue;
                    }
                    $stmt->bindValue(1 + (COLUMN_NUMBER * $i), $_POST["insert_checkbox1"][$key]);
                    $stmt->bindValue(2 + (COLUMN_NUMBER * $i), $_POST["insert_checkbox2"][$key]);
                    $stmt->bindValue(3 + (COLUMN_NUMBER * $i), $_POST["insert_title"][$key]);
                    $i++;
                }
                $stmt->execute();
                
            }
            foreach ($_POST["update_title"] as $key => $value) {
                if (($_POST["update_delete"][$key] == "1") || ((!$_POST["update_title"][$key]) && $_POST["update_checkbox1"][$key] == "0" && $_POST["update_checkbox2"][$key] == "0")) {
                    $update_add_delete[] = $key;
                    $update_add_delete_placeholder .= "?,";
                    
                } else {
                    $update_sql[$key] = "UPDATE list SET check1=?,check2=?,`name`=? WHERE id=?";
                }
            }
            foreach ($update_sql as $key => $value) {
                $stmt = $pdo->prepare($value);
                $stmt->bindValue(1, $_POST["update_checkbox1"][$key]);
                $stmt->bindValue(2, $_POST["update_checkbox2"][$key]);
                $stmt->bindValue(3, $_POST["update_title"][$key]);
                $stmt->bindValue(4, $key);
                $stmt->execute();
                
        
            }
            if ($update_add_delete_placeholder) {
                $update_add_delete_placeholder = "(".substr($update_add_delete_placeholder, 0, -1) . ")";
                $stmt = $pdo->prepare("DELETE FROM list WHERE id IN {$update_add_delete_placeholder}");
                for ($i = 1; $i <= count($update_add_delete); $i++) {
                    $stmt->bindValue($i, $update_add_delete[$i - 1]);
                }
                $stmt->execute();
            }
        } catch (PDOException $e) {
            print_r($e->getMessage());
            exit;
        }
    }
}
$result = array();
try {
    $pdo = db();
    $stmt = $pdo->query("SELECT * FROM list");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    print_r($e->getMessage());
    exit;
}
$token = uniqid();
$_SESSION["token"] = $token;

?>

<html>

<head>
    <script src="js/sample.js?<?= date("YmdHis") ?>"></script>
</head>

<body>
    <div>
        <div><span>php.iniからsqlite3を有効にして試してください</span></div>
        <form>
            <p data-list-insert-number="0" data-list="true">
                <input type="hidden" name="insert_checkbox1[0]" value="0">
                <input type="hidden" name="insert_checkbox2[0]" value="0">
                <span>項目1</span><input type="checkbox" name="insert_checkbox1[0]" value="1">
                <span>項目2</span><input type="checkbox" name="insert_checkbox2[0]" value="1">
                <span>タイトル</span><input type="text" class="title" name="insert_title[0]">
            </p>
            <?php
            foreach ($result as $r) {
                $check1 = "";
                $check2 = "";
                if ($r["check1"]) {
                    $check1 = "checked";
                }
                if ($r["check2"]) {
                    $check2 = "checked";
                }
                $html = <<<EOM
            <p data-list-update-number="{$r['id']}" data-list="true" style="">
            <input type="hidden" name="update_checkbox1[{$r['id']}]" value="0">
            <input type="hidden" name="update_checkbox2[{$r['id']}]" value="0">
            <span>項目1</span><input type="checkbox" name="update_checkbox1[{$r['id']}]" value="1" $check1>
            <span>項目2</span><input type="checkbox" name="update_checkbox2[{$r['id']}]" value="1" $check2>
            <span>タイトル</span><input type="text" class="title" name="update_title[{$r['id']}]" value="{$r["name"]}">
            <input type="button" data-update-delete="{$r['id']}" value="削除">
            <input type="hidden" name="update_delete[{$r['id']}]" data-update-delete-value="{$r['id']}" value="0">
        </p>
EOM;
                echo $html;
            }
            ?>
            <input type="hidden" name="token" value="<?= $token ?>">
        </form>
        <p>
            <button type="button" id="add">追加</button>
        </p>
        <p>
            <button type="button" id="regist">登録</button>
        </p>
    </div>
</body>

</html>