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
        <div class ='float-right'>
        <div class='col-12' style='margin-top:10px;'>     
                    <a href='?controller=index&action=additem&id=<?php echo -1?> '> alles
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                        </svg> 
                    </a> 
                </div>
        <?php 
        ?>
        </div>
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
        //var_dump($items);
        foreach($items as $item){
            print_r($item['name_item']);
            echo "<br> ";
            print_r($item['price_item']);
            echo "<br> ";
        }
    }
}


?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
