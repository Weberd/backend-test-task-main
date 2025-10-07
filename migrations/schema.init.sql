create table if not exists products
(
    uuid char(36) primary key comment 'UUID товара',
    category  varchar(255) not null comment 'Категория товара',
    is_active tinyint default 1  not null comment 'Флаг активности',
    servce_type varchar(255) not null comment 'Тип услуги',
    description text null comment 'Описание товара',
    thumbnail  varchar(255) null comment 'Ссылка на картинку',
    price decimal(10, 2) not null comment 'Цена',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP comment 'Дата создания',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP comment 'Дата обновления'
) comment 'Товары';

create index is_active_idx on products (is_active);
