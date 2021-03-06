<?php
session_start();
require_once('../action/db.inc.php');
require_once('./tpl/tpl-html-head.php');
require_once('./tpl/header.php');
require_once("./tpl/func-buildTree.php");
require_once("./tpl/func-getRecursiveCategoryIds.php");
?>

<div class="container-fluid">
  <div class="row">
    <!-- 樹狀商品種類連結 -->
    <div class="col-md-3"><?php buildTree($pdo, 0); ?></div>

    <!-- 商品項目清單 -->
    <div class="col-md-9">
      <?php
      if (isset($_GET['itemId'])) {
        //SQL 敘述
        $sql = "SELECT `items`.`itemId`, `items`.`itemName`, `items`.`itemImg`, `items`.`itemPrice`, 
                        `items`.`itemQty`, `items`.`itemCategoryId`, `items`.`created_at`, `items`.`updated_at`,
                        `categories`.`categoryId`, `categories`.`categoryName`
                    FROM `items` INNER JOIN `categories`
                    ON `items`.`itemCategoryId` = `categories`.`categoryId`
                    WHERE `itemId` = ? ";

        $arrParam = [
          $_GET['itemId']
        ];


        //查詢
        $stmt = $pdo->prepare($sql);
        $stmt->execute($arrParam);

        //若商品項目個數大於 0，則列出商品
        if ($stmt->rowCount() > 0) {
          $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-5">
                <div class="row mb-3 d-flex justify-content-center">
                  <img class="item-view border" src="../images/items/<?php echo $arr[0]["itemImg"] . '.png'; ?>">
                </div>
                <div class="row">
                  <img class="item-preview img-thumbnail border" src="../images/items/<?php echo $arr[0]["itemImg"] . ".png"; ?>" alt="...">
                  <?php
                  //找出預覽圖片
                  $sqlMultipleImages = "SELECT `multipleImageId`, `multipleImageImg`
                                            FROM `multiple_images` 
                                            WHERE `itemId` = ?";
                  $stmtMultipleImages = $pdo->prepare($sqlMultipleImages);
                  $stmtMultipleImages->execute($arrParam);
                  if ($stmtMultipleImages->rowCount() > 0) {
                    $arrMultipleImages = $stmtMultipleImages->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = 0; $i < count($arrMultipleImages); $i++) {
                  ?>
                      <img class="item-preview img-thumbnail border" src="../images/multiple_images/<?php echo $arrMultipleImages[$i]['multipleImageImg']; ?>" alt="...">
                  <?php
                    }
                  }
                  ?>
                </div>
              </div>
              <div class="col-md-7">
                <h5 class="mt-3 ml-3 "><?php echo $arr[0]["itemName"]; ?></h5>
                <p class="mt-3 ml-3">NT$<?php echo $arr[0]["itemPrice"]; ?></p>
                <form name="cartForm" id="cartForm" method="POST" action="./addCart.php">
                  <label class="mt-3 ml-3">數量: </label>
                  <input type="text" name="cartQty" id="cartQty" value="1" maxlength="5">
                  <div  class="d-flex ">
                    <button type="button" class="btn btn-outline-secondary btn-lg mt-4 ml-3" id="btn_addCart" data-item-id="<?php echo $_GET['itemId'] ?>">加入購物車</button>
                  </div>
                  <input type="hidden" name="itemId" id="itemId" value="<?php echo $_GET['itemId'] ?>">
                </form>
              </div>
            </div>
           
            <div class="row"><?php require_once("./tpl/tpl-comments-list.php"); ?></div>
            <div class="row"><?php require_once("./tpl/tpl-comments.php"); ?></div>
          </div>

      <?php
        }
      }
      ?>
    </div>
  </div>
</div>

<?php
require_once('./tpl/footer.php');
require_once('./tpl/tpl-html-foot.php');
?>