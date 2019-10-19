<!-- Extension directive -->
<?php $this->extendsView('views/middle_page.php'); ?>

<?php $this->section('menu') ?>
    <a href="index.php" title="To home page">Home</a>|<a href="https://it.linkedin.com/in/lorenzosanzari" target="_blank" title="My LinkedIn profile">About me</a>
<?php $this->endsection() ?>

<?php $this->section('title') ?>

    <img src="../logo.svg" title="Mozaiku logo" style="margin: 20px auto;display: block; clear: both;">
    <?php $this->parentContent() //middle page title content ?>

<?php $this->endsection() ?>


<!-- Stack use example -->
<?php $this->push('head') ?>
<style>
    body {font-size: 1.3em;color: #999;font-family: "Trebuchet MS",Verdana,Arial,sans-serif;}
    body a {color: #595 !important;}
    #title {font-size: 36px;}
    #menu {text-align: right; width: 100%;}
    #article, #footer {margin-top: 35px; border: 1px solid #aaa;padding: 15px 25px 15px 25px;}
    #title, #footer {text-align: center;width: 100%;}
</style>
<?php $this->endpush() ?>

<?php $this->push('footer') ?>
<script>
    console.log("Welcome on a Mozaiku example page! :-)"); //see browser console!
</script>
<?php $this->endpush() ?>



