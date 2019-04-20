 <script>
    $(document).ready(function() {
        $('.dropdown' ).hover(
            function(){
                // $(this).children('.sub-menu').slideDown(100);
            },
            function(){
                $(this).children('.sub-menu').slideUp(100);
            }
        );
        $('.dropdown').click(function(){
            $(this).children('.sub-menu').slideDown(100);
        })
    }); // end ready
    function tampilkanjam(){
        var waktu = new Date();
        var jam = waktu.getHours();
        var menit = waktu.getMinutes();
        var detik = waktu.getSeconds();
        var teksjam = new String();
        if ( menit <= 9 )
        menit = "0" + menit;
        if ( detik <= 9 )
        detik = "0" + detik;
        teksjam = jam + ":" + menit + ":" + detik;
        tempatjam.innerHTML = teksjam;
        setTimeout ("tampilkanjam()",1000);
    }
    function start() {
        tampilkanjam();
    }
    window.onload = start;
    </script>

</head>

<header>
    <p><h2>CMS | Church Membership System - GMI GLORIA</h2></p>
    <div class="mynav">
    <ul>
    <?php
        $sub="";
        $plain = "";
        $n=0;
        foreach($sqlmenu as $data)
        {
            $n++;
            $x = print_recursive_list($data['child']);
            $menuexe = $data['menuexe']=="0"?"#":base_url().$data['menuexe'];
            $menuexe = $data['link']!=''?$data['link']:$menuexe;
            if($x!=""){
                ?>
                <li class="dropdown <?php echo $data['menuicon'] ?>">
                    <a class="havedropdown">
                        <?php echo $data['menuname'] ?>
                    </a>
                    <ul class="sub-menu">
                        <?php echo print_recursive_list($data['child']); ?>
                    </ul>
                </li>
                <?php
            }
            else{
                ?>
                <li class="dropdown <?php echo $data['menuicon'] ?>">
                    <a href="<?=  $menuexe ?>">
                        <?php echo $data['menuname'] ?>
                    </a>
                </li>
                <?php
            }
        }
    ?>
    <div class="indukJam" id="easyui-menubutton">
            <?php echo date("d M Y"); ?> / <span id="tempatjam"></span>
        </div>
    </ul>

    </div>

    <div style="margin:10px 0;"></div>
</header>