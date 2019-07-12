package net.artron.controller;

import net.artron.feign.ShopJavaFeignClient;
import net.artron.feign.ShopSidecarFeignClient;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.client.RestTemplate;

@RestController
public class SidecarClientController {

    @Autowired
    private RestTemplate restTemplate;

    @Autowired
    private ShopSidecarFeignClient shopSidecarFeignClient;

    @Autowired
    private ShopJavaFeignClient shopJavaFeignClient;

    @GetMapping("/request_shop_java_with_feign")
    public String request_shop_java_with_feign() {
        return this.shopJavaFeignClient.goodses();
    }

    @GetMapping("/request_shop_sidecar_with_feign")
    public String request_shop_sidecar_with_feign() {
        return this.shopSidecarFeignClient.goodses();
    }

    @GetMapping("/request_shop_sidecar_with_rest_template")
    public String request_with_rest_template() {
        return this.restTemplate.getForObject("http://shop-sidecar/goodses", String.class);
    }

    @GetMapping("/request_shop_java_with_rest_template")
    public String request_shop_java_with_rest_template() {
        return this.restTemplate.getForObject("http://shop-java/goodses", String.class);
    }
}


