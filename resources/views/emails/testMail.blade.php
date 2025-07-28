@component('mail::message')
# 備份完成通知 📦

您好，您的資料庫備份已成功完成！

- 備份檔案：**filename**
- 備份大小：**size**
- 備份時間：**$time**

@component('mail::button', ['url' => 'https://yourdomain.com/download?file=' . 'filename'])
下載備份檔案
@endcomponent

感謝您使用我們的服務！

@endcomponent
