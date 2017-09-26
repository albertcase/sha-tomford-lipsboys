# SHA-Kenzo-Recruitment`s API

### 1. 小样提交信息API

Method: POST

##### API URL:

```html
domian/api/giftinfo
```
##### Get Parameter

name: 张三, moblile: 13112345678, province:上海, city:上海, area:黄浦区, address:湖滨路

```javascript
{
name: '张三',
tel: '13112345678',
province: '上海',
city: '上海',
area: '黄浦区',
address: '湖滨路'
}
```


##### Response

##### status 1

```javascript
{
status: '1',
msg: '信息提交成功',
}
```

#####  status 0

```javascript
{
status: '0',
msg: '信息提交失败',
userStatus: {
    "isold": 0,
    "isgift": 1,
    "issubmit": 0,
    "isluckydraw": 0
  }
}
```

---

### 2. 领取小样API

Method: POST

##### API URL:

```html
domian/api/gift
```
##### Get Parameter
null

```javascript
{

}
```

##### Response

##### status 1

```javascript
{
status: '1',
msg: '小样领取成功',
userStatus: {
    "isold": 0,
    "isgift": 1,
    "issubmit": 1,
    "isluckydraw": 0
  }
}
```

#####  status 0

```javascript
{
status: '0',
msg: '非新关注用户没有领取资格',
userStatus: {
    "isold": 0,
    "isgift": 0,
    "issubmit": 0,
    "isluckydraw": 0
  }
}
```

#####  status 2

```javascript
{
status: '2',
msg: '今天小样已经领取完毕，请明天再来。',
userStatus: {
    "isold": 0,
    "isgift": 0,
    "issubmit": 0,
    "isluckydraw": 0
  }
}
```

#####  status 3

```javascript
{
status: '3',
msg: '小样已经全部领空。',
userStatus: {
    "isold": 0,
    "isgift": 0,
    "issubmit": 0,
    "isluckydraw": 0
  }
}
```

#####  status 4

```javascript
{
status: '4',
msg: '对不起，您已经领取过小样！',
userStatus: {
    "isold": 0,
    "isgift": 0,
    "issubmit": 0,
    "isluckydraw": 0
  }
}
```

---

### 3. 抽奖API

Method: POST

##### API URL:

```html
domian/api/lottery
```
##### Get Parameter
null

```javascript
{

}
```

##### Response

##### status 1

```javascript
{
status: '1',
msg: '恭喜中奖',
userStatus: {
    "isold": 0,
    "isgift": 1,
    "issubmit": 1,
    "isluckydraw": 1
  }
}
```

#####  status 0

```javascript
{
status: '0',
msg: '遗憾未中奖',
userStatus: {
    "isold": 0,
    "isgift": 0,
    "issubmit": 0,
    "isluckydraw": 0
  }
}
```

#####  status 2

```javascript
{
status: '2',
msg: '今天的奖品已经发没，请明天再来！',
userStatus: {
    "isold": 0,
    "isgift": 0,
    "issubmit": 0,
    "isluckydraw": 0
  }
}
```

#####  status 3

```javascript
{
status: '3',
msg: '您已获奖',
userStatus: {
    "isold": 0,
    "isgift": 0,
    "issubmit": 0,
    "isluckydraw": 0
  }
}
```

---

### 4. 获取图片验证码API

Method: GET

##### API URL:

```html
domian/api/picturecode
```
##### Get Parameter
null

```javascript
{

}
```

##### Response

##### status 1

```javascript
{
status: '1',
picture: BASE64,
}
```

---

### 5. 发送短信验证码API

Method: GET

##### API URL:

```html
domian/api/phonecode
```
##### Get Parameter
null

```javascript
{

}
```

##### Response

##### status 1

```javascript
{
status: '1',
msg: 'send ok'
}
```

---

### 6. 验证图片验证码API

Method: POST

##### API URL:

```html
domian/api/checkpicture
```
##### Get Parameter
picture=asdf

```javascript
{
    picture: 'asdf'
}
```

##### Response

##### status 1

```javascript
{
status: '1',
msg: 'success'
}
```

##### status 0

```javascript
{
status: '0',
msg: 'picture code is failed'
}
```

---


### 7. 验证短信验证码API

Method: POST

##### API URL:

```html
domian/api/checkphonecode
```
##### Get Parameter
phonecode=1234

```javascript
{
    phonecode: 1234
}
```

##### Response

##### status 1

```javascript
{
status: '1',
msg: 'success'
}
```

##### status 0

```javascript
{
status: '0',
msg: 'phone code is failed'
}
```
