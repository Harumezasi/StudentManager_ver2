# StudentManager_ver2
2018 Capstone Design Project - Student Management System -  Ver 0.2

## Requirement
#### PHP Extensions
1. curl
2. gd2
3. fileinfo
4. intl
5. mbstring
6. xmlrpc

#### Laravel - Version 5.6.17ex
<pre>
// Laravel Session
composer requrie predis/predis

// Laravel Flash Message
composer require laracasts/flash

// Laravel Excel Library
composer require maatwebsite/excel
composer require phpoffice/phpspreadsheet

// Laravel - Vue.js 다국어 연동 => 자세한 사항은 다음의 URL 참조
/* https://github.com/martinlindhe/laravel-vue-i18n-generator */
composer require martinlindhe/laravel-vue-i18n-generator

// Eloquent 모델 확장 기능 
/* https://github.com/topclaudy/compoships */
composer require awobaz/compoships

// CURL을 이용한 HTTP Request 라이브러리
/* https://github.com/guzzle/guzzle */
composer require guzzlehttp/guzzle
</pre>

#### Vue.js
<pre>
// Vue.js 사용을 위한 최소한의 설치
npm install vue         
npm install vue-route
npm install vue-axios
npm install axios

// 
npm install vuetify
npm install vue-js-modal

// Laravel - Vue.js 다국어 연동 
npm i --save vue-i18n

// 
npm install moment
npm install vue-chart-js
npm install vue-chartjs
</pre>

## Maintenance

#### 국가공휴일 데이터 획득 권한 갱신
국가공휴일 데이터 획득을 위해서, 서비스 요청키를 주기적으로 갱신할 필요가 있음.<br>
참고 URL : https://www.data.go.kr/dataset/15012690/openapi.do<br>
마지막 갱신일 : 2018년 6월
