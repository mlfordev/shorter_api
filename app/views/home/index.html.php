<div class="app">
    <div class="block">
        <h1 class="title">Сокращатель ссылок</h1>
        <form action="/short-links" method="post" class="shorter" accept-charset="utf-8">
            <input type="text" name="url" placeholder="Вставьте сюда ссылку" class="shorter__input" formenctype="multipart/form-data" required>
            <button type="submit" class="shorter__button">Сократить</button>
        </form>
        <div class="short-link">
            <span>Короткая ссылка: </span><a class="short-link__value" target="_blank"></a>
        </div>
        <div class="error">Неизвестная ошибка</div>
    </div>
</div>
<div class="overlay"></div>