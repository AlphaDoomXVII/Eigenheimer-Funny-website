<?php
namespace Eigenheimer\View;

class UI {
    static public function navbar() {
        $buttons = []; 
        $buttons[] = ['title' => 'Home', 'url' => 'http://example.com/button1' , 'class' => ''];
        $buttons[] = ['title' => 'Bestellen', 'url' => 'http://example.com/button2' , 'class' => '' ];
        $buttons[] = ['title' => 'Routes', 'url' => 'http://example.com/button3' , 'class' => ''];
        $buttons[] = ['title' => 'Over ons', 'url' => 'http://example.com/button4' , 'class' => ''];
        if($buttons){
            ?>  <nav class="navbar navbar-expand-lg navbar-light bg-info">
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                
                <?php
            foreach($buttons as $button){
                //title & url
                ?>
                            <a class ="nav-item nav-link <?php echo $button['class']; ?>" href="<?php echo $button['url']; ?>"><?php echo $button['title']; ?></a>
                <?php
            }
            ?>          </div>
                    </div>
                </nav> 
            <?php
        }
    }
    static public function footer($buttons)
    {
        if($buttons){
            ?>  <nav class="navbar navbar-expand-lg navbar-light bg-dark">
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                
                <?php
            foreach($buttons as $button){
                //title & url
                ?>
                            <a class ="nav-item nav-link" href="<?php echo $button['url']; ?>"><?php echo $button['title']; ?></a>
                <?php
            }
            ?>          </div>
                    </div>
                </nav> 
            <?php
        }
    }

    static public function items($data)
    {
        if(!$data) {exit();}
        ?> 
        <div class="container">
            <div class="row ">
        <?php
        foreach ($data as $row){
            ?>
            <div class="col-3 ">
                <form method="post"> 
                    <input type="hidden" name="selector" id="selector" value="additem">
                    <input type="hidden" name="price_item" id="price_item" value="<?php echo $row['price'] ?>">
                    <input type="hidden" name="uuid_item" id="uuid_item" value="<?php echo $row['UUID'] ?>">
                    <input type="hidden" name="name_item" id="name_item" value="<?php echo $row['name'] ?>">
                    <input type="submit" name="button1" class="button" value="+" /> 
                </form> 



                    <?php echo $row['name'] . "<br>&euro;" . $row['price'] ?>
                    <a href='?controller=index&action=additem&id=<?php echo $row['id']?> '> <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/></svg></a>
                    
            </div>
            <?php } ?>
            </div>
        </div> 
        <?php
            
        }
   static public function show_basket($items)
    {
    if(!isset($items) || empty($items)) {
        $items = [];
    }
    ?> 
    <ul class ='float-right list-group col-3' > 
    <?php 
    foreach($items as $item){
        ?>
        <li class='list-group-item float-left'>
            <form method="post">
                <input type="hidden" name="selector" id="selector" value="removeitem">
                <input type="hidden" name="basket_item_uuid" id="basket_item_uuid" value="<?php echo $item['basket_item_uuid'] ?>">
                <input type="hidden" name="uuid_item" id="uuid_item" value="<?php echo $item['uuid_item'] ?>">
                <button type="submit" class="btn btn-danger float-left">
                    &nbsp;<i class="bi bi-trash"></i>
                    &nbsp;
                </button>
            </form>
            <?php echo '&nbsp;'.$item['name_item'] ?>
            <div class='float-right'> &euro;<?php echo $item['price_item']; ?></div>
        </li> 
        <?php
    }
    ?>
    </ul> 
    <?php 
    }

}



?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">