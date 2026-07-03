<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title><?=$title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="<?=$db->site('title')?>">
    <meta name="keywords" content="<?=$db->site('keywords')?>">
    <meta name="description" content="<?=$db->site('description')?>">

    <meta property="og:type" content="website">
    <meta property="og:url" content="<?=$_SERVER['HTTP_HOST']?>">
    <meta property="og:title" content="<?=$db->site('title')?>">
    <meta property="og:image" content="<?=DOMAIN.$db->site('anhbia')?>">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?=$_SERVER['HTTP_HOST']?>">
    <meta property="twitter:title" content="<?=$db->site('title')?>">
    <meta property="twitter:description" content="<?=$db->site('description')?>">
    <meta property="twitter:image" content="<?=DOMAIN.$db->site('anhbia')?>">

    <meta name="author" content="<?=$db->site('author')?>">
    <link rel="icon" href="<?=DOMAIN.$db->site('favicon')?>" type="image/x-icon" />
    <script>
        var csrf_token = "<?= generate_csrf_token() ?>"
    </script>
    <link rel="stylesheet" href="/assets/css/doilacloi.css" />

    <!-- GLightBox -->
    <link rel="stylesheet" href="/assets/css/glightbox.min.css" />
    <!-- Aos -->
    <link rel="stylesheet" href="/assets/css/aos.css" />
    <!-- Nice Select -->
    <link rel="stylesheet" href="/assets/css/nice-select.css" />
    <!-- Quill CSS -->
    <link href="/assets/css/quill.core.css" rel="stylesheet" />
    <link href="/assets/css/quill.snow.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Font-awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css?v=<?php echo time(); ?>">
    <link href="/assets/css/font-awesome-all.min.css" rel="stylesheet" />
    <link href="/assets/css/fontawesome.css" rel="stylesheet" />
    <!-- Swiper CSS -->
    <link href="/assets/css/swiper-bundle.min.css" rel="stylesheet" />
    <!-- Main CSS -->
    <link href="/assets/css/style.css" rel="stylesheet" />
    <link href="/assets/css/job_post.css" rel="stylesheet" />
    <!-- Responsive CSS -->
    <link href="/assets/css/resposive.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/styles.css?v=<?php echo time(); ?>">
    <!-- Simple Notify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@1.0.4/dist/simple-notify.css" />
    <!-- Simple Notify JS -->
    <script src="https://cdn.jsdelivr.net/npm/simple-notify@1.0.4/dist/simple-notify.min.js"></script>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.0/css/boxicons.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Signika:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
         * {
            font-family: 'Signika', sans-serif ;
            letter-spacing: 0.5px;
        }

        .pagination {
            display: inline-block;
        }

        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color 0.3s;
            border-radius: 50%;
            border: 1px solid #B4B4B4;
            margin: 0 4px;
            font-size: 18px;

        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination a.active {
            background-color: #ff6900;
            color: white;
            border: 1px solid #ff6900;
        }

        .pagination a:first-child,
        .pagination a:last-child {
            border-radius: 50%;
        }

        .shop-widget-btn {
            width: 100%;
            font-size: 15px;
            padding: 10px 20px;
            border-radius: 8px;
            color: #39404a;
            background: #e8e8e8;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            transition: all linear .3s;
            -webkit-transition: all linear .3s;
            -moz-transition: all linear .3s;
            -ms-transition: all linear .3s;
            -o-transition: all linear .3s
        }

        .shop-widget-btn:hover {
            color: #fff;
            background: #ff6900
        }

        .shop-widget-btn i {
            margin-right: 8px;
            margin-top: -1px
        }
        .qr-code {
            pointer-events: none;
        }
    </style>
    <style>
    .gigs-img {
        position: relative;
        overflow: hidden;
    }

    .gigs-img img {
        transition: transform 0.3s ease;
    }

    .gigs-img:hover img {
        transform: scale(1.1);
    }

    .gigs-img .user-thumb img {
        transition: none;
        transform: none; 
    }

    .gigs-img:hover {
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
    }

    .gigs-img::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(1);
        width: 70px;
        height: 70px;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .gigs-img:hover::after {
        opacity: 1;
    }
    </style>
    <script>
        function showMessage(message, type) {
            const commonOptions = {
                effect: 'fade',
                speed: 300,
                customClass: null,
                customIcon: null,
                showIcon: true,
                showCloseButton: true,
                autoclose: true,
                autotimeout: 3000,
                gap: 20,
                distance: 20,
                type: 'outline',
                position: 'right top'
            };

            const options = {
                success: {
                    status: 'success',
                    title: 'Thành công!',
                    text: message,
                },
                error: {
                    status: 'error',
                    title: 'Thất bại!',
                    text: message,
                }
            };
            new Notify(Object.assign({}, commonOptions, options[type]));
        }
    </script>

</head>