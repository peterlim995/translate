<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <title>자막 텍스트 처리기</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- bootstrap 추가 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .text_area {
            width: 100%;
            height: 300px;
            margin-top: 10px;
        }

        .loading-feedback,
        .loading-feedback-gpt {
            position: fixed;
            /* 고정 위치 */
            top: 50%;
            /* 상단으로부터 화면의 50% 지점 */
            left: 50%;
            /* 왼쪽으로부터 화면의 50% 지점 */
            transform: translate(-50%, -50%);
            /* 요소의 중앙을 정확한 중앙에 맞춤 */
            display: none;
            z-index: 9999;
        }
    </style>

    @stack('css')
</head>

<body>
    <div class="loading-feedback">
        <img src="{{ asset('/images/loading-loading-forever.gif') }}" alt="Loading..."
            style="width: 30px; height: 30px;" />
    </div>
    <div class="loading-feedback-gpt">
        <img src="{{ asset('/images/loading-loading-forever.gif') }}" alt="Loading..."
            style="width: 30px; height: 30px;" />
    </div>

    @include('header')
    @yield('content')

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    @stack('scripts')

</body>

</html>
