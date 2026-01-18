{{ config(materialized='view') }}

select
    order_id,
    customer_id,
    order_date::date as order_date,
    order_amount,
    status,
    created_at
from {{ source('raw', 'sales_orders') }}
where status != 'CANCELLED'
