<!DOCTYPE html>
<html>
    <head>
        <title>Mozaiku example theme</title>
        <?php $this->stack('head'); //css contents defined in page.php?>
    </head>
    <body>
        <div id="menu">
        <?php $this->showsection('menu'); //defined in page.php ?>
        </div>
        
            <div id="title">
            <?php $this->section('title'); // overridden by middle_page.php  ?>
                Theme title content
            <?php $this->endsection() ?>
            </div>
        
        
            <div id='article'>
                <?php $this->section('article') //overridden by middle_page.php ?>
                <strong>This is the article from THEME</strong>

                <?php $this->section('subarticle') //overridden by partial.php (included in middle_page.php) ?>
                Subarticle di THEME
                <?php $this->endsection() ?>

                ...and other content of THEME article...

                <?php $this->endsection() ?>


            </div>
        <div id="footer">
            <?php $this->showsection('footer') ?>
        </div>
        <?php $this->stack('footer') //defined in page.php ?>
    </body>

</html>