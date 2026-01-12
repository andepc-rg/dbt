{{ config(materialized='table') }}

select
    order_id,
    customer_id,
    order_date,
    order_amount,
    created_at
from {{ ref('stg_sales_orders') }}
