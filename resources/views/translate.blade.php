@extends('layout')
@section('content')
    @push('css')
        <style>

        </style>
    @endpush

    <div class="mt-5">
        <div class="container mb-4">
            <div class="row">
                <div class="col-6 mb-4">
                    <h3>자막 텍스트 입력</h3>
                    <textarea class="text_area" id="inputText" placeholder="여기에 자막 텍스트를 붙여넣으세요."></textarea>
                    <button class="btn btn-primary" id="processText">
                        텍스트 처리
                    </button>
                    <button class="btn btn-primary" id="clearText">
                        Clear
                    </button>
                </div>
                <div class="col-6">
                    <h3>결과</h3>
                    <textarea class="text_area" id="outputText"></textarea>
                    <button class="btn btn-primary mr-3" id="deepLTranslate">
                        DeepL 번역
                    </button>
                    <button class="btn btn-primary" id="gptTranslate">
                        ChatGpt 번역
                    </button>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-6 mb-4">
                    <h3>한국어 번역 (DeepL)</h3>
                    <textarea class="text_area" id="deepLResult"></textarea>
                </div>
                <div class="col-6">
                    <h3>한국어 번역 (ChatGPT)</h3>
                    <textarea class="text_area" id="gptResult"></textarea>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-6 mb-4">
                    <h3>영어 제거</h3>
                    <textarea class="text_area" id="removeEnglish"></textarea>
                    <button class="btn btn-primary" id="removeEnglishBtn">
                        영어 제거
                    </button>
                    <button class="btn btn-primary" id="removeEnglishClear">
                        Clear
                    </button>
                </div>
                <div class="col-6">
                    <h3>자막 검사</h3>
                    <textarea class="text_area" id="subtitleCheck"></textarea>
                    <button class="btn btn-primary" id="subtitleCheckBtn">
                        검사 시작
                    </button>
                    <button class="btn btn-primary" id="subtitleCheckClear">
                        Clear
                    </button>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {
                $("#processText").click(function() {
                    var inputText = $("#inputText").val();
                    // 숫자, 시간 표시 및 화살표 부분을 제거 (빈 줄도 포함하여 처리)
                    var processedText = inputText.replace(
                        /^\d+\s*\n\d{2}:\d{2}:\d{2}\.\d{3} --> \d{2}:\d{2}:\d{2}\.\d{3}\s*\n/gm,
                        ""
                    );
                    // 빈 줄 제거
                    processedText = processedText.replace(/^\s*[\r\n]/gm, "");

                    // 문장을 하나로 합치고
                    processedText = processedText.replace(/\n/g, " ");

                    // 단어 수가 50개를 넘어가고 마침표가 있을 때 줄바꿈 처리
                    var words = processedText.split(" ");
                    var newProcessedText = "";
                    var wordCount = 0;

                    for (var i = 0; i < words.length; i++) {
                        newProcessedText += words[i] + " ";
                        wordCount++;

                        // 단어 수가 50을 넘고 마침표(.)로 끝나는 경우 줄바꿈 추가
                        if (wordCount > 30 && words[i].endsWith(".")) {
                            newProcessedText += "\n\n";
                            wordCount = 0; // 단어 수 초기화
                        }
                    }

                    $("#outputText").val(newProcessedText.trim());
                });

                // Clear 버튼 클릭 시 입력, 출력 텍스트 영역 초기화
                $("#removeEnglishClear").click(function() {
                    $("#removeEnglish").val("");
                });

                $("#clearText").click(function() {
                    $("#inputText").val("");
                    $("#outputText").val("");
                    $("#deepLResult").val("");
                    $("#gptResult").val("");
                });


                $("#deepLTranslate").click(function() {
                    var textToTranslate = $("#outputText").val();

                    if (textToTranslate === "") {
                        alert("번역할 텍스트가 없습니다.");
                        return;
                    }

                    $(".loading-feedback").show();

                    $.ajax({
                        url: '/deepl',
                        type: "POST",
                        data: {
                            //token
                            _token: "{{ csrf_token() }}",

                            text: textToTranslate,
                        },
                        success: function(response) {
                            $(".loading-feedback").hide();
                            console.log(response);
                            // 번역된 텍스트를 '결과' 텍스트 영역에 출력
                            $("#deepLResult").val(response.translatedText);
                        },
                        error: function(xhr, status, error) {
                            $(".loading-feedback").hide();
                            console.error("번역 오류", status, error);
                        },
                    });
                });
            });

            $("#gptTranslate").click(function() {
                var textToTranslate = $("#outputText").val();

                // console.log("textToTranslate: ", textToTranslate);
                if (textToTranslate === "") {
                    alert("번역할 텍스트가 없습니다.");
                    return;
                }

                // AJAX 요청을 설정합니다.
                $(".loading-feedback-gpt").show();

                $.ajax({
                    url: "/gpt",
                    type: "POST",
                    data: {
                        //token
                        _token: "{{ csrf_token() }}",
                        text: textToTranslate,
                    },
                    success: function(response) {
                        $(".loading-feedback-gpt").hide();
                        console.log("response: ", response);
                        // 번역된 텍스트를 '결과' 텍스트 영역에 출력
                        $("#gptResult").val(response.translatedText);
                    },
                    error: function(xhr, status, error) {
                        $(".loading-feedback-gpt").hide();
                        console.error("번역 오류", status, error);
                    },
                });

                // $.ajax({
                // //   url: "https://api.openai.com/v1/translations",
                //   url: "https://api.openai.com/v1/chat/completions",
                //   type: "POST",
                //   headers: {
                //     "Content-Type": "application/json",
                //     Authorization: "Bearer " + openAiApiKey,
                //   },
                //   data: JSON.stringify({
                //     // model: "text-davinci-003", // 사용할 모델 지정, 번역에 적합한 모델로 변경 가능
                //     model: "gpt-3.5-turbo", // 사용할 모델 지정, 번역에 적합한 모델로 변경 가능
                //     input: textToTranslate, // 번역할 텍스트
                //     source_language: "EN", // 원본 언어
                //     target_language: "KO", // 목표 언어
                //   }),
                //   success: function (response) {
                //     // API 응답으로 받은 번역된 텍스트를 출력 영역에 설정
                //     $("#gptResult").val(response.choices[0].text);
                //   },
                //   error: function (xhr, status, error) {
                //     console.error("번역 오류", status, error);
                //     $("#gptResult").val("번역 중 오류가 발생했습니다.");
                //   },
                // });
            });

            // $("#removeEnglishBtn").click(function() {
            //     var text = $('#removeEnglish').val();

            //     // 각 줄을 검사하여 영어 문자가 포함된 줄을 제거합니다.
            //     var lines = text.split('\n'); // 줄바꿈으로 줄을 분리합니다.
            //     var filteredLines = lines.filter(function(line) {
            //         return !/[a-zA-Z]/.test(line); // 영어 문자가 포함되지 않은 줄만 남깁니다.
            //     });
            //     var modifiedText = filteredLines.join('\n'); // 남은 줄들을 다시 줄바꿈 문자로 연결합니다.

            //     // 수정된 텍스트를 다시 textarea에 설정합니다.
            //     $('#removeEnglish').val(modifiedText);

            // });

            $("#removeEnglishBtn").click(function() {
                var text = $('#removeEnglish').val();

                // 각 줄을 검사하여 영어와 한국어가 함께 있는 줄만 남깁니다.
                var lines = text.split('\n'); // 줄바꿈으로 줄을 분리합니다.
                var filteredLines = lines.filter(function(line) {
                    return !/[a-zA-Z]/.test(line) || (/[a-zA-Z]/.test(line) && /[가-힣]/.test(
                        line)); // 영어 문자만 포함된 줄은 제거하고, 영어와 한국어가 함께 있는 줄은 남깁니다.
                });
                var modifiedText = filteredLines.join('\n'); // 남은 줄들을 다시 줄바꿈 문자로 연결합니다.

                // 수정된 텍스트를 다시 textarea에 설정합니다.
                $('#removeEnglish').val(modifiedText);

            });


            $("#directTranslation").click(function() {
                var caption = $("#inputText").val();
                var korean = $("#deepLResult").val();


                // AJAX 요청을 설정합니다.
                $(".loading-feedback-gpt").show();

                $.ajax({
                    url: "/translateTotal",
                    type: "POST",
                    data: {
                        //token
                        _token: "{{ csrf_token() }}",
                        caption: caption,
                        korean: korean,
                    },
                    success: function(response) {
                        $(".loading-feedback-gpt").hide();
                        console.log("response: ", response);
                        // 번역된 텍스트를 '결과' 텍스트 영역에 출력
                        $("#koreanSubtitle").val(response.koreanSubtitle);
                    },
                    error: function(xhr, status, error) {
                        $(".loading-feedback-gpt").hide();
                        console.error("번역 오류", status, error);
                    },
                });
            });

            // $('#subtitleCheckBtn').click(function() {
            //     var subtitles = $('#subtitleCheck').val();
            //     // 각 라인을 분리
            //     var lines = subtitles.split('\n');
            //     var correctedSubtitles = [];
            //     for (var i = 0; i < lines.length; i++) {
            //         var line = lines[i].trim();
            //         if (line) {
            //             // 숫자만 있는 라인 판별
            //             if (line.match(/^\d+$/)) {
            //                 // 숫자 다음 라인(시간 코드)과의 공백 제거
            //                 correctedSubtitles.push(line);
            //                 continue;
            //             }
            //             // 시간 코드 판별
            //             if (line.match(/^\d{2}:\d{2}:\d{2}\.\d{3} --> \d{2}:\d{2}:\d{2}\.\d{3}$/)) {
            //                 correctedSubtitles.push(line);
            //                 continue;
            //             }
            //             // 자막 텍스트 라인
            //             correctedSubtitles.push(line);
            //             // 다음 라인이 숫자 라인이면 공백 라인 추가
            //             if (i + 1 < lines.length && lines[i + 1].trim().match(/^\d+$/)) {
            //                 correctedSubtitles.push('');
            //             }
            //         }
            //     }
            //     $('#subtitleCheck').val(correctedSubtitles.join('\n')); // 수정된 자막을 textarea에 출력
            // });


            $('#subtitleCheckBtn').click(function() {
                var subtitles = $('#subtitleCheck').val();
                var lines = subtitles.split('\n');
                var correctedSubtitles = [];
                var previousWasText = false;

                for (var i = 0; i < lines.length; i++) {
                    var line = lines[i].trim();
                    if (!line) continue; // 빈 줄은 무시

                    // 숫자만 있는 라인 판별
                    if (line.match(/^\d+$/)) {
                        if (previousWasText) {
                            correctedSubtitles.push(''); // 자막 텍스트 후 공백 라인 추가
                        }
                        correctedSubtitles.push(line);
                        previousWasText = false;
                        continue;
                    }

                    // 시간 코드 판별
                    if (line.match(/^\d{2}:\d{2}:\d{2}\.\d{3} --> \d{2}:\d{2}:\d{2}\.\d{3}$/)) {
                        correctedSubtitles.push(line);
                        continue;
                    }

                    // 자막 텍스트 라인
                    correctedSubtitles.push(line);
                    previousWasText = true; // 텍스트 라인을 기록
                }

                $('#subtitleCheck').val(correctedSubtitles.join('\n')); // 수정된 자막을 textarea에 출력
            });


            $('#subtitleCheckClear').click(function() {
                $('#subtitleCheck').val('');
            });
        </script>
    @endpush
@endsection
