package net.artron.repository;

import net.artron.entity.Goods;
import org.springframework.data.repository.PagingAndSortingRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface GoodsRepository extends PagingAndSortingRepository<Goods, Long> {
}
