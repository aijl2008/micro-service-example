package net.artron.feign;

import org.springframework.cloud.netflix.feign.FeignClient;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;

@FeignClient(name = "shop-java")
public interface ShopJavaFeignClient {
    @RequestMapping(value = "", method = RequestMethod.GET)
    public String goodses();
}
