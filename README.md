# SHA-Tomford-Lipsboys API

## 初始化操作
```
 1.生成场次 php /vagrant/scipt/create_applylist.php
 2.模拟登陆 http://127.0.0.1:9122/wechat/same/callback?openid=1231231&redirect_uri=1231
```
### 1. 发送短信验证码API

Method: POST

##### API URL:

```html
domian/api/phonecode
```
##### POST Parameter
null

```javascript
{
  mobile : 13112311231
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

### 2. 验证短信验证码API

Method: POST

##### API URL:

```html
domian/api/checkphonecode
```
##### POST Parameter
phone=13112311231&phonecode=1234

```javascript
{
    phone: 13112311231
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

---

### 3. 预约API

Method: POST

##### API URL:

```html
domian/api/apply
```
##### POST Parameter
name=pm&timeslot=2017-10-21 am&phone=13112311231

```javascript
{
    name: pm
    timeslot: 2017-10-21 am
    phone: 13112311231
}
```

##### Response

##### status 1

```javascript
{
status: '1',
msg: 'apply success'
}
```

##### status 0

```javascript
{
status: '0',
msg: 'apply failed'
}
```

##### status 2

```javascript
{
status: '2',
msg: 'apply num is null'
}
```

##### status 3

```javascript
{
status: '3',
msg: 'apply again'
}
```

---

### 4. 获取预约场次列表API

Method: POST

##### API URL:

```html
domian/api/applylist
```
##### POST Parameter
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
msg: 'get apply list success',
data: [
  {"name":'2017-10-21 am', "num":2},
  {"name":"2017-10-21 pm", "num":0}
]
}
```

##### status 0

```javascript
{
	status: '0',
	msg: 'get apply list failed'
}
```
---

### 5. 核销API

Method: POST

##### API URL:

```html
domian/api/consume
```
##### POST Parameter
code=123

```javascript
{
	code: 123
}
```

##### Response

##### status 1

```javascript
{
	status: '1',
	msg: '核销成功',
}
```

##### status 0

```javascript
{
	status: '0',
	msg: '核销失败'
}
```
##### status 2

```javascript
{
	status: '2',
	msg: '核销码错误'
}
```
##### status 3

```javascript
{
	status: '3',
	msg: '该核销码已经核销过'
}
```