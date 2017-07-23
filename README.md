# Students  
(русская версия ниже)

Try this site: http://shinoa.web44.net/

Educational project: you can **browse** list of students, use **search**, add **your** own data, which will give you cookie token, so you could **edit** your 'Student profile' later.

There're currently two versions: written without use of frameworks and psr-7, and the Slim version (Slim is tiny framework). Big framework will come next!

We use mysql/MariaDB with two tables: one holding data about **profiles**, another manipulates **password** hashes.

## Used technologies

1. PHP 7.1.2
2. MariaDB 10.1.21
3. Composer 
4. Twig
5. PHPUnit
6. PHPDoc

## Prerequisites: 

1. PHP >= 7.0, apache with document root in /public folder.
2. MySQL/MariaDB with two tables: [here you can download needed dumps](http://zalivalka.ru/359753).
3. Composer (all dependencies lie in .json)
4. Twig templating engine
5. PHPUnit
6. PHPDoc 

A few words about Project's structure: https://pastebin.com/Zj1c993A

--- 

[Рабочая копию этого сайта (последний релиз)](http://shinoa.web44.net/): http://shinoa.web44.net/

Учебный проект: вы можете просматривать список студентов, пользоваться поиском, добавлять свои собственые данные на сайт, тем самым получая "членство", сохраняемое в куки; посредством этого вы можете в дальнейшем редактировать свои данные в удобной форме.

Мы используем для этой цели mysql/MariaDB с двумя таблицами: одна хранит данные о студентах, другая - о хэшах их паролей.

Есть две ветки проекта: master (старая) версия без использования фреймворков и psr-7, и Slim, с использованием одноименного микро-фреймворка.

## Использованные технологии

1. PHP 7.1.2
2. MariaDB 10.1.21
3. Composer 
4. Twig
5. PHPUnit
6. PHPDoc

## Зависимости: 

1. PHP >= 7.0, apache с document root в папке /public.
2. MySQL/MariaDB с двумя таблицами: [здесь вы можете их загрузить](http://zalivalka.ru/359753).
3. Composer (все зависимости можете найти в .json)
4. Twig templating engine
5. PHPUnit
6. PHPDoc

Несколько слов о классах проекта: https://pastebin.com/Zj1c993A


