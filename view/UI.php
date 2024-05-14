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

static public function items($amount)
{
    ?><div class="container">
    <div class="row">
     <?php
    while ($amount > 0){
        ?>
        <div class="col-3 ">
                One of three columns 
        </div>
    <?php $amount -= 1;
    }
     ?>
    </div>
  </div> <?php
}
}
?>





<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
