<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>
      <?php
        $n= 0 ;
        foreach($sqlmenu as $data){
          $n++;
          $x = print_recursive_sidebar($data['child']);
          $menuexe = $data['menuexe']=="0"?"#":base_url().$data['menuexe'];
          $menuexe = $data['link']!=''?$data['link']:$menuexe;
          if($x!=""){
                ?>
                <li class='treeview'>
                    <a>
                       <i class='<?= $data['menuicon'] ?>'></i>
                        <?php echo $data['menuname'] ?>
                         <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php echo print_recursive_sidebar($data['child']); ?>
                    </ul>
                </li>
                <?php
            }
            else{
                ?>
                <li>
                    <a href="<?=  $menuexe ?>" >
                        <i class='<?= $data['menuicon'] ?>'></i>
                        <?php echo $data['menuname'] ?>
                    </a>
                </li>
                <?php
            }
        }
      ?>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>