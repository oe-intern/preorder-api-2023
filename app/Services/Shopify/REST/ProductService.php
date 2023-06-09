<?php

namespace App\Services\Shopify\REST;

use App\Services\Shopify\BaseService;
use App\Models\Product;
use App\Services\Shopify\REST\ShopService;
use App\Models\User;

class ProductService extends BaseService
{
  public function show()
  {
    $response = $this->getshop()->api()->rest('GET', '/admin/products.json');
    return $response["body"]["products"];
  }
  /**
   * Get products data from shopify
   *
   * @return array
   */
  protected function getProductsData()
  {

    $query = '{
            products(first:10,query:"availableForSale:false"){
              edges {
                node {
                  id
                  title
                  status
                  handle
                  images(first: 5) {
                    edges {
                      node {
                        src
                      }
                    }
                  }
                  variants(first: 10) {
                    edges {
                      node {
                        title
                        price
                        availableForSale
                        compareAtPrice
                        image {
                          src
                        }
                      }
                    }
                  }
                }
              }
            }
          }';

    // $response = $this->getShop()->api()->graph($query);
    // return $response['body']['data']['products'];


    $response = $this->getShop()->api()->rest('GET', '/admin/products.json');
    return data_get($response, 'body.products');
  }

  /**
   * Get all products
   *
   * @return array
   */
  public function getAllProducts()
  {
    return $this->getProductsData();
  }

  public function getAllProductsFromShopify()
  {
    $response = $this->getShop()->api()->rest('GET', '/admin/products.json');
    return data_get($response, 'body.products');
  }

  /**
   * Get a specific product by ID
   *
   * @param  int  $productId
   * @return array|null
   */
  public function getProductById( $productId)
  {

    $query = "{
            product(id: \"gid://shopify/Product/{$productId}\") {
                id
                title
                status
                handle
                images (first: 5) {
                    edges {
                        node {
                            src
                        }
                    }
                }
                variants (first: 10) {
                    edges {
                        node {
                            title
                            price
                            availableForSale
                            compareAtPrice
                            image {
                                src
                            }
                        }
                    }
                }
            }
        }";
      
    $response = $this->getShop()->api()->graph($query);
    return $response['body']['data']['product'];


    // $response = $this->getShop()->api()->rest('GET', "/admin/products/{$productId}.json");
    // return data_get($response, 'body.product');
  }
}
