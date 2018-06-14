*** 파일 덮어쓰기 후 ...

// 필수
1. 클래스 오토로드 설정
composer dump-autoload

2. 라이브러리 설치
composer require awobaz/compoships





// 여유 있을 때 설치
1. 라이브러리 설치
composer require guzzlehttp/guzzle

2. .env 파일 수정
HOLIDAY_DATA_KEY=X5q3RV8Km5fwgK%2Fu22ghOh2Tbl8fH0YBygDPYaTg%2BUuHjLH3suTGLA8kKEWo1hI%2BPmyElwXM5mu4noGreJGmYg%3D%3D

3. config/app.php 파일에 다음 내용을 추가
    /**
     *  ------------------------------
     *  국가공휴일 데이터 조회용 인증키
     *  ------------------------------
     *
     *  국가공휴일 데이터를 얻기 위해 필요한 인증키를 정의.
     *  인증키 유효기한은 발급 후 2년. (2018년 기준)
     *
     *  ### 국가공휴일 데이터 획득 URL : https://www.data.go.kr/dataset/15012690/openapi.do
     */

    'holiday_data_key' => env('HOLIDAY_DATA_KEY', NULL),

4. 데이터베이스 재조정
php artisan migrate:reset
php artisan migrate
php artisan db:seed

5. php artisan 명령어 목록 확인
php artisan list

holiday 항목에 holiday:add 함수가 추가되었는지 확인

6. 해당 명령어 실행
php artisan holiday:add

7. 데이터베이스에 공휴일 정보가 저장되었는 지 확인
select * from schedules
