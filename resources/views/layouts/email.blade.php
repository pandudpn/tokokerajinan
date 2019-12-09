    <link href="https://fonts.googleapis.com/css?family=Bitter|Work+Sans&display=swap" rel="stylesheet">
    <div style="font-family: 'Work Sans', sans-serif;background-color: rgb(245, 245, 245);height: max-content;padding: 10px 30px;">
        <a href="http://www.tokokerajinan.id">
            <img src="http://www.tunasmitrabangsa.com/assets/logo.png" style="display: block;margin-left: auto;margin-right: auto;">
        </a>
        <div style="margin-top: 2%;padding: 0px 200px;margin-bottom: 2%;">
            <div style="background-color: #fff;border-radius: 2px;box-shadow: 0px 0px 1px rgba(0,0,0,.5);padding: 2px 10px;">
                <h5 style="font-size: 17px;font-family: 'Bitter', serif;">Hello {{ $nama }}</h5>
                <p style="color: #7c7c7c;">Email <b>{{ $email }}</b> telah terdaftar pada <a style="color: rgba(0,0, 255, .5);text-decoration: none;" href="http://www.tokokerajinan.id/">tokokerajinan.id</a>.</p>
                <p style="color: #7c7c7c;font-size: 13px;margin-top: 6%;">Silahkan selesaikan pendaftaran dengan mengklik tombol dibawah ini.</p>
                <div style="text-align: center; margin-top: 6%; margin-bottom: 3%;">
                    <a href="{{ url('/user/confirmation/'.$id) }}" style="text-align:center; display:inline-block; box-shadow: 0px 0px 1px rgba(0,0,0,.4); font-size: 18px; font-weight: bold;padding:15px; background: #ff914d; transition: .4s; border-radius: 10px; width:165px; text-decoration:none; color:#fff;" onmouseover="this.style.background='#e06112'; this.style.color='#f0f0f0'" onmouseout="this.style.background='#ff914d'; this.style.color='#fff';">Konfirmasi Email</a>
                </div>
            </div>
        </div>
    </div>