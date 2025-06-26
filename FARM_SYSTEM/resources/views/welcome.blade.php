<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>PUP-T FARM</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets-landing-page/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets-landing-page/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets-landing-page/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets-landing-page/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets-landing-page/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets-landing-page/css/main.css" rel="stylesheet">

</head>

<body class="index-page">

  <header id="header" class="header sticky-top">
    <div class="branding d-flex align-items-cente">
      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="{{route ('welcome')}}" class="logo d-flex align-items-center">
            <img src="{{ asset('assets/images/pup-logo.png') }}" width="50" height="50" alt="Logo">
            <div>
                <h6 class="sitename mb-0" style="color: #800000; font-weight:bold;" >PUP-T FARM</h6> 
                <div class="sub-title" style="color: #3d405c;">Faculty Academic Requirements Management</div>
            </div>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#home" class="active">Home</a></li>
            <li><a href="#announcement">Announcement</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="#how-it-works">How it Works</a></li>
            <li><a href="#developer">Developer</a></li>
            <li><a href="#testimonials">Testimony</a></li>

    
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

      </div>

    </div>

  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="home" class="hero section light-background">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
            <h1><span>Simplifying Faculty Workflows at PUP Taguig</span></h1>
            <p>Manage, Track, and Fulfill Academic Requirements with Ease</p>
               <div class="d-flex">
            <!-- Faculty Login with OAuth -->
             <a href="{{ $oauthUrl }}" class="btn-get-started">Faculty Login</a>
        
            <!-- Admin Login using Route -->
            <a href="{{ route('login') }}" class="btn-get-started ms-3" style="background-color: #FFDF00; color: black; border: 1px solid #FFDF00;">Admin Login</a>
        </div>


          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->

       <!-- Announcement Section -->
       <section id="announcement" class="pricing section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Announcement</h2>
            <p><span>See the recent</span> <span class="description-title">Announcement</span></p>
        </div><!-- End Section Title -->
    
        <div class="container">
    
            <div class="row gy-3">
                @if($announcement)
                <div class="col-xl-12 col-lg-12" data-aos="fade-up" data-aos-delay="200">
                    <div class="pricing-item featured">
                        <h3 class="subject">{{ $announcement->subject }}</h3>
                        <div class="message">
                          {!! $announcement->message !!}
                        </div>
                    </div>
                </div><!-- End Announcement Item -->
                @else
                <div class="col-xl-12 col-lg-12" data-aos="fade-up" data-aos-delay="200">
                    <div class="pricing-item featured">
                        <p>No recent announcements available.</p>
                    </div>
                </div><!-- End Announcement Item -->
                @endif
            </div>
        </div>
    </section><!-- AnnouncementSection -->

    <!-- About Section -->
    <section id="about" class="about section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>About</h2>
        <p><span>Find Out More</span> <span class="description-title">About FARM</span></p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-3">

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <img src="assets-landing-page/img/about-page.png" alt="" class="img-fluid">
          </div>

          <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="200">
            <div class="about-content ps-0 ps-lg-3">
              <h3>FACULTY ACADEMIC REQUIREMENTS MANAGEMENT</h3>
              <p class="fst-italic">
                FARMS is a state-of-the-art system developed exclusively for PUP-Taguig faculty members. Designed to streamline the process of 
                managing academic requirements, FARMS ensures that faculty can focus on what matters most: delivering quality education. Our mission 
                is to simplify administrative tasks, improve efficiency, and enhance collaboration within the academic community.
              </p> 
            </div>
          </div>
        </div>
      </div>

    </section><!-- /About Section -->

    <!-- Services Section -->
    <section id="features" class="services section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>FEATURES</h2>
        <p><span>Feature</span> <span class="description-title">Highlights</span></p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-folder"></i>
              </div>
              <div class="stretched-link">
                <h3>Centralized Document Management</h3>
            </div>
            
              <p>Easily store, access, and manage all academic requirements in one place.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-activity"></i>
              </div>
              <div class="stretched-link">
                <h3>Real-time Tracking</h3>
            </div>            
              <p>Monitor the progress of academic submissions and approvals with instant updates</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-bell"></i>
              </div>
              <div class="stretched-link">
                <h3>Automated Notifications</h3>
            </div>
              <p>Receive alerts for upcoming deadlines and required actions, keeping you ahead of schedule</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-person-badge"></i>
              </div>
              <div class="stretched-link">
                <h3>User-friendly Interface</h3>
            </div>
              <p>Intuitive design tailored for faculty needs, ensuring a smooth experience.</p>

            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-shield-lock"></i>
              </div>
              <div class="stretched-link">
                <h3>Secure and Reliable</h3>
            </div>
              <p>Built with the latest security protocols to keep your data safe and accessible only to you</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-megaphone"></i>
              </div>
              <div class="stretched-link">
                <h3>Announcement</h3>
            </div>
              <p>Admins will post updates and important information here to keep users informed.</p>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services Section -->

  <!-- Featured Services Section -->
  <section id="how-it-works" class="featured-services section  light-background">

    <div class="container section-title" data-aos="fade-up">
      <h2>How does it work?</h2>
      <p><span>How it</span> <span class="description-title">Works</span></p>
    </div><!-- End Section Title -->

    <div class="container">

      <div class="row gy-4">

        <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
          <div class="service-item position-relative">
            <div class="icon"><i class="bi bi-folder"></i></div>
            <h4><a href="#!" class="stretched-link" style="cursor: default;">Upload Requirements</a></h4>

            <p>Easily submit documents or academic requirements directly through the platform</p>
          </div>
        </div><!-- End Service Item -->

        <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="200">
          <div class="service-item position-relative">
            <div class="icon"><i class="bi bi-speedometer2"></i></div>
            <h4><a href="#!" class="stretched-link" style="cursor: default;">Track Progress</a></h4>
            <p>Monitor the status of each submission in real-time, with progress bars and checklists.</p>
          </div>
        </div><!-- End Service Item -->

        <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="300">
          <div class="service-item position-relative">
            <div class="icon"><i class="bi bi-chat-square-text"></i></div>
            <h4><a href="#!" class="stretched-link" style="cursor: default;">Receive Feedback</a></h4>
            <p>Get notifications and updates from department heads instantly</p>
          </div>
        </div><!-- End Service Item -->

        <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="400">
          <div class="service-item position-relative">
            <div class="icon"><i class="bi bi-check-circle"></i></div>
            <h4><a href="#!" class="stretched-link" style="cursor: default;">Complete Tasks</a></h4>
            <p>Finalize and close out pending requirements with a few clicks</p>
          </div>
        </div><!-- End Service Item -->

      </div>

    </div>

  </section><!-- /Featured Services Section -->

    
    <!-- Developer Section -->
    <section id="developer" class="team section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Developer</h2>
        <p><span>Our Hardworking</span> <span class="description-title">Developer</span></p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
            <div class="team-member">
              <div class="member-img">
                <img src="assets-landing-page/img/developer-3.jpg" class="img-fluid" alt="">
             
              </div>
              <div class="member-info">
                <h4>James Nabayra</h4>
                <span>Project Manager / Developer</span>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="400">
            <div class="team-member">
              <div class="member-img">
                <img src="assets-landing-page/img/developer-2.jpg" class="img-fluid" alt="">

              </div>
              <div class="member-info">
                <h4>Ed Judah Mingo</h4>
                <span>Lead Developer</span>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="team-member">
              <div class="member-img">
                <img src="assets-landing-page/img/developer-1.jpg" class="img-fluid" alt="">
              </div>
              <div class="member-info">
                <h4>Diana Rose Fidel</h4>
                <span>Quality Assurance / Developer</span>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="300">
            <div class="team-member">
              <div class="member-img">
                <img src="assets-landing-page/img/developer-4.png" class="img-fluid" alt="">
              </div>
              <div class="member-info">
                <h4>Kazel Villamarzo</h4>
                <span>Document Analyst / Developer</span>
              </div>
            </div>
          </div><!-- End Team Member -->

        </div>

      </div>

    </section><!-- Developer Section -->

   
    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section dark-background">

      <img src="assets-landing-page/img/testimony-bg.png" class="testimonials-bg" alt="">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              }
            }
          </script>
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets-landing-page/img/female-profile.png" class="testimonial-img" alt="">
                <h3>Prof. Maria Dela Cruz</h3>
                <div class="stars">
                 
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>FARMS has made managing my academic workload so much easier. It’s intuitive and efficient</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets-landing-page/img/male-profile.png" class="testimonial-img" alt="">
                <h3>Prof. John Santos</h3>
                <div class="stars">
                 
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>I can now track my submissions and get updates instantly. No more missed deadlines!</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </div>
    </section><!-- /Testimonials Section -->
   
  </main>

  <footer id="footer" class="footer">

    <div class="container footer-top">
        <div class="row gy-4">
          <div class="col-lg-6 col-md-12 footer-about">
            <a href="{{route ('welcome')}}" class="d-flex align-items-center">
              <span class="sitename">PUP-T FARM</span>
            </a>
            <div class="footer-contact pt-3">
              <p>PUP-Taguig, Gen. Santos Ave,</p>
              <p>Lower Bicutan, Taguig City</p>
              <p class="mt-3"><strong>Phone:</strong> <span>+63 2 1234 5678</span></p>
              <p><strong>Email:</strong> <span>farmsupport@pup-taguig.edu.ph</span></p>
            </div>
          </div>
      
          <div class="col-lg-6 col-md-12 footer-links">
            <h4>Quick Links</h4>
            <ul>
              <li><i class="bi bi-chevron-right"></i> <a href="#home">Home</a></li>
              <li><i class="bi bi-chevron-right"></i> <a href="#about">About us</a></li>
              <li><i class="bi bi-chevron-right"></i> <a href="#features">Features</a></li>
              <li><i class="bi bi-chevron-right"></i> <a href="#developer">Developer</a></li>
              <li><i class="bi bi-chevron-right"></i> <a href="#announcement">Announcement</a></li>
              <li><i class="bi bi-chevron-right"></i> <a href="#testimonials">Testimony</a></li>
            </ul>
          </div>
        </div>
      </div>
      
    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright 2024 - </span> <strong class="px-1 sitename">PUP-Taguig</strong> <span>All Rights Reserved</span></p>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader">
    <div></div>
    <div></div>
    <div></div>
    <div></div>
  </div>

  <!-- Vendor JS Files -->
  <script src="assets-landing-page/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets-landing-page/vendor/php-email-form/validate.js"></script>
  <script src="assets-landing-page/vendor/aos/aos.js"></script>
  <script src="assets-landing-page/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets-landing-page/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets-landing-page/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets-landing-page/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets-landing-page/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets-landing-page/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets-landing-page/js/main.js"></script>

</body>

</html>