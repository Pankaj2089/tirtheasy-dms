<header class="header fixed-top bg-white">
    <!--Main header start-->
    <nav class="navbar navbar-expand-lg">
        <div class="logo-left d-flex align-items-center gap-3">
            <button class="border-0 outline-0 humberger-ic" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                <img src="{{$siteUrl}}public/assets/img/humberger.svg" />
            </button>
            <a class="navbar-brand comp-logo" href=".">
                <img src="{{$siteUrl}}public/assets/img/logo.svg" class="img-fluid" alt="">
            </a>
        </div>

        <!-- toggler menu start -->
        <!-- <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header">
                <button type="button" class="btn-close-bl" data-bs-dismiss="offcanvas" aria-label="Close">
                    <img src="{{$siteUrl}}public/assets/img/arrow-left.png" alt="">
                </button>
                </div>
                <div class="offcanvas-body">
                <ul class="menu_nav">
                    <li><a href=".">Home</a></li>
                </ul>                
                </div>
            </div> -->
        <!-- toggler menu end -->
        <div class="search-box d-flex align-items-center">
            <img src="{{$siteUrl}}public/assets/img/search-ic.svg" alt="">
            <input type="text" class="search-input border-0 shadow-none bg-transpaent"
                placeholder="Search icons...">
        </div>
        <div class="head-menu ms-md-auto ">
            <ul class="d-flex align-items-center list-style-none">
                <li class="d-none d-lg-block"><a href="/">Home</a></li>
                <li class="d-none d-lg-block"><a href="">Share Link</a></li>
                <li class="d-none d-lg-block"><a href="/">Reservation</a></li>
                <li class="d-none d-lg-block"><a class="theme-btn text-white" data-bs-toggle="modal"
                        data-bs-target="#reservation-pop">Quick Reservation</a></li>
                <li data-bs-toggle="modal" data-bs-target="#reservation-new"><img src="{{$siteUrl}}public/assets/img/notification-ic.svg"
                        alt=""></li>
                <li class="profile"><img src="{{$siteUrl}}public/assets/img/buid-ic.svg" alt=""></li>
            </ul>
        </div>
    </nav>
    <!--Main header end-->
</header>