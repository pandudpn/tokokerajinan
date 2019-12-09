@extends('layouts.frontend')
@section('content')
    <div class="container" style="margin-top: 4%;">
        <div class="col-md-6 mx-auto">
            <div class="_activation">
                <div class="col-md-12 text-center" id="icon"></div>
                <p id="pesan"></p>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            var id  = '{{ Request::segment(3) }}';
            ConfirmUser(id);
        });

        function ConfirmUser(id){
            var base    = '{!! url("/api/user/confirm") !!}';
            var URL     = base + '/' + id
            $.ajax({
                url: URL,
                type: 'GET',
                dataType: 'json',
                cache: false,
                success: function(data){
                    if(data.status === 200){
                        $('#icon').addClass('text-success');
                    }else if(data.status === 406){
                        $('#icon').addClass('text-warning');
                    }else if(data.status === 404){
                        $('#icon').addClass('text-danger');
                    }
                    $('#icon').html(data.icon);
                    $('#pesan').html(data.pesan);
                }
            })
        }
    </script>
@endsection