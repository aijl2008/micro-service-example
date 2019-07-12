package net.artron.feign;

import feign.hystrix.FallbackFactory;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class ShopJavaFeignClientFallbackFactor implements FallbackFactory {
    private static final Logger LOGGER = LoggerFactory.getLogger(ShopSidecarFeignClientFallbackFactory.class);

    @Override
    public ShopJavaFeignClient create(Throwable cause) {
        return new ShopJavaFeignClient() {
            public String goodses() {
                ShopJavaFeignClientFallbackFactor.LOGGER.info("fallback; reason was:", cause);
                return "ShopJavaFeignClientFallbackFactor";
            }
        };
    }
}
