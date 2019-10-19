<?php $this->extendsView('views/theme.php'); ?>

<?php $this->section('title') // overridden by page.php ?>
    Plain PHP Template inheritance
<?php $this->endsection() ?>


<?php $this->section('article') ?>

<h2>Using mozaiku is really simple and powerful!..</h2>

<?php $this->includeView('views/partial.php') //override theme.php 'subarticle' section ?>

<p>Visit: <a href="https://github.com/lorenzosanzari/mozaiku" target="_blank">https://github.com/lorenzosanzari/mozaiku</a></p> 

<?php $this->endsection() ?>


<?php $this->section('footer') ?>
<p>Mozaiku - Plain PHP Template inheritance - @author: <a href="https://it.linkedin.com/in/lorenzosanzari" target="_blank" title="My LinkedIn profile">Lorenzo Sanzari</a> &copy; 2019</p>
<?php $this->endsection() ?>











