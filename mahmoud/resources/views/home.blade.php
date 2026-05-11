<!doctype html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ env('APP_NAME') }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:400,400i,700">
  <link href="{{ asset('css/bootstrap.rtl.min.css')}}" rel="stylesheet">
  <link href="{{ asset('css/all.css')}}" rel="stylesheet">
  <script src="{{ asset('js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('js/all.min.js')}}"></script>
  <link rel="stylesheet" href="{{ asset('css/style.css?v=6')}}">

</head>

<body>
  <!-- partial:index.partial.html -->
  <div class="top-header">
    <div class="container ">
      <div class="row">
        <div class="col-md-4">
          <div class="logo"><img src="{{ asset('img/logo.png')}}" ></div>
        </div>
        <div class="col-md-8">
          <div class="logo-text">محمود شملخ</div>
          <div class="social_media ">
            <a href="https://www.facebook.com/mahmoud.shamallakh.9?mibextid=ZbWKwL" target="_blank"><i class="fab fa-facebook "></i></a>
            <a href="#" target="_blank"><i class="fab fa-twitter "></i></a>
            <a href="https://wa.me/972595587292" target="_blank"> <i class="fab fa-whatsapp "></i></a>
            <a href="Mahmoud.sh.2021@gmail.com" ><i class="fa fa-envelope "></i></a>
          </div>
          <div class="social_media my-2"><i class="fa fa-phone "></i> <span dir=ltr> +972595587292 </span></div>
        </div>




      </div>
    </div>


  </div>
  <br><br>
  <div class="container ">
    <div class="row">
      @foreach ($photos as $photo )
      <div class="col-md-4">
        <a href="{{ asset('storage/'.$photo->image)}}" target="_blank">
          <div class="gal-div my-3">

            <img src="{{ asset('storage/'.$photo->image)}}">
            <figcaption>{{ $photo->name }}</figcaption>

          </div>
        </a>

      </div>
      @endforeach

      {{ $photos->withQueryString()->links() }}



    </div>

  </div>

  <div class="footer">
     <div class="container ">
      <div class="row">
        <div class="col-md-6"><div class="footer_text">محمود شملخ </div></div>
        <div class="col-md-6">
        <div class="social_media ">
            <a href="https://www.facebook.com/mahmoud.shamallakh.9?mibextid=ZbWKwL" target="_blank"><i class="fab fa-facebook "></i></a>
            <a href="#" target="_blank"><i class="fab fa-twitter "></i></a>
            <a href="https://wa.me/972595587292" target="_blank"> <i class="fab fa-whatsapp "></i></a>
            <a href="Mahmoud.sh.2021@gmail.com" ><i class="fa fa-envelope "></i></a>
          </div>

        </div>
      </div>
     </div>
  </div>

  <!-- partial -->

</body>

</html>