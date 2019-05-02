#  XE-LIFO Comment Translator(Powered by NAVER™ OpenAPI)

> XE 에서 사용할 수 있도록 제작된 댓글 번역 애드온 입니다.

![GitHub](https://img.shields.io/github/license/LIFOsitory/xe-Naver.openapi-papago.svg?style=flat-square)
![GitHub release](https://img.shields.io/github/release/LIFOsitory/xe-Naver.openapi-papago.svg?style=flat-square)

### XE

XpressEngine(XE)은 누구나 쉽고 편하고 자유롭게 콘텐츠를 발행을 할 수 있도록 하기 위한 CMS(Content Management System)입니다. 

자세한 내용은 [XE](https://github.com/xpressengine/xe-core)에서 확인하세요.

### NAVER™ OPENAPI PAPAGO

네이버 서비스에서 사용하고 있는 파파고 기능을 고객의 서비스에 활용하여 고객이 댓글을 원하는 언어로 변역할 수 있습니다. 

자세한 내용은 [네이버™ 개발자센터](https://developers.naver.com/products/nmt/)에서 확인하세요.

## 💾 Install

- 릴리즈에서 최신 버전의 소스를 다운로드 합니다.
- 압축을 풀고 폴더 이름을 **naver_openapi_papago** 로 변경합니다.
- XE의 addons 폴더 안으로 이동시킵니다.

### API 사용 신청

- NAVER™ OpenAPI를 이용하므로 API 이용신청이 필요합니다.
- [여기](https://developers.naver.com/apps/#/register?defaultScope=captcha)에서 애플리케이션을 등록합니다.
- Papago 언어감지, Papago NMT 번역, Papago SMT 번역을 사용 API에 추가하셔야 합니다.

### API 소개
- [언어 감지](https://developers.naver.com/products/detectLangs/)
- [Papago NMT](https://developers.naver.com/products/nmt/)
- [Papago SMT](https://developers.naver.com/products/translator/)

## 🔨 Usage

- 관리자 페이지에서 설치된 에드온 목록을 확인합니다.
- NAVER™ OpenAPI Papago 애드온을 설정합니다.
- NAVER™ 개발자 센터에서 클라이언트 ID와 Secret을 받아 입력합니다.
- 기타 설정을 완료한 뒤 저장합니다.
- PC 또는 Mobile에 체크합니다.

### Based on XEDITION Skin
XE 기본 스킨인 XEDITION을 기반으로 만들어졌습니다.

번역하기 버튼이 올바르게 뜨기 위해서는 다음과 같은 구조와 클래스명을 가져야 합니다.

```
<li class="fbItem" id="comment_1284">
    ...
    <!--BeforeComment(1284,0)-->
    <div class="comment_1284_0 xe_content">
        Comment Contents
    </div>
    <!--AfterComment(1284,0)-->									
    <p class="action">
        <a href="#" onclick="translateContext($(this)); return false;" class="translating-comment" style="display: inline;"><i class="xi-exchange"></i> 번역하기</a>
        ...		
        <a class="comment_1284 this" href="#popup_menu_area" onclick="return false">이 댓글을</a>   			
    </p>
</li>
```
❗️ 필수 클래스명 : fbItem, action

❗️ 필수 아이디값 : comment_srl (ex. comment_1284)

> 내부 디자인은 view.css 를 통해 수정할 수 있습니다. 




### Limit
- 제휴신청은 API를 일 호출 허용량 이상으로 사업적으로 사용하기 위해 API 사용량, API 사용처, API 활용목적에 대해 검토를 받는 절차이며 API 사용처, 활용 목적에 따라 제휴승인이 거절될 수 있습니다.

#### Language Detection
- 처리한도(무료) : 2000000자/일
- [네이버™ 개발자센터](https://developers.naver.com/apps/#/cooperation/apply)에서 제휴 신청할 수 있습니다.

#### NMT

- 처리한도(무료) : 10000자/일
- [네이버™ 클라우드 플랫폼](https://www.ncloud.com/product/applicationService/papagoNmt)에서 제휴 신청할 수 있습니다.

#### SMT 

- 처리한도(무료) : 10000자/일
- [네이버™ 클라우드 플랫폼](https://www.ncloud.com/product/applicationService/papagoSmt)에서 제휴 신청할 수 있습니다.

❗️❗️ 처리한도 이상으로 사용시 번역 기능은 작동하지 않습니다.

### Language
#### NMT
- 한국어(ko)-영어(en)
- 한국어(ko)-일본어(ja)
- 한국어(ko)-중국어 간체(zh-CN)
- 한국어(ko)-중국어 번체(zh-TW)
- 한국어(ko)-스페인어(es)
- 한국어(ko)-프랑스어(fr)
- 한국어(ko)-러시아어(ru)
- 한국어(ko)-베트남어(vi)
- 한국어(ko)-태국어(th)
- 한국어(ko)-인도네시아어(id)
- 한국어(ko)-독일어(de)
- 한국어(ko)-이탈리아어(it)
- 중국어 간체(zh-CN)-중국어 번체(zh-TW)
- 중국어 간체(zh-CN)-일본어(ja)
- 중국어 번체(zh-TW)-일본어(ja)
- 영어(en)-일본어(ja)
- 영어(en)-중국어 간체(zh-CN)
- 영어(en)-중국어 번체(zh-TW)
- 영어(en)-프랑스어(fr)
#### SMT
- 한국어(ko)-영어(en)
- 한국어(ko)-일본어(ja)
- 한국어(ko)-중국어 간체(zh-CN)
- 한국어(ko)-중국어 번체(zh-TW)

## 📜 License

This software is licensed under the [LGPL-3.0](https://github.com/LIFOsitory/xe-Naver.openapi-papago/blob/master/LICENSE) © [LIFOsitory](https://github.com/LIFOsitory).