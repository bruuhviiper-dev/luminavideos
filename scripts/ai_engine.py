import sqlite3
import json
import sys
import os
import re
import math
from collections import Counter

def get_words(text):
    if not text:
        return []
    # Remover pontuação e transformar em minúsculo
    text = re.sub(r'[^\w\s]', '', str(text).lower())
    # Remover stopwords simples
    stopwords = {'o', 'a', 'os', 'as', 'de', 'do', 'da', 'dos', 'das', 'em', 'no', 'na', 'nos', 'nas', 'um', 'uma', 'para', 'com', 'que', 'e', 'é'}
    return [w for w in text.split() if w not in stopwords and len(w) > 2]

def cosine_similarity(vec1, vec2):
    intersection = set(vec1.keys()) & set(vec2.keys())
    numerator = sum([vec1[x] * vec2[x] for x in intersection])

    sum1 = sum([vec1[x]**2 for x in vec1.keys()])
    sum2 = sum([vec2[x]**2 for x in vec2.keys()])
    denominator = math.sqrt(sum1) * math.sqrt(sum2)

    if not denominator:
        return 0.0
    else:
        return float(numerator) / denominator

def main():
    if len(sys.argv) > 1:
        user_id = sys.argv[1]
    else:
        user_id = None

    db_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'database', 'database.sqlite')
    
    if not os.path.exists(db_path):
        print(json.dumps({'error': 'Database not found'}))
        sys.exit(1)

    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()

    # Buscar todos os vídeos públicos e ativos
    cursor.execute("SELECT id, category_id, title, description, views_count FROM videos WHERE visibility = 'public' AND status = 'active'")
    all_videos = cursor.fetchall()
    
    video_dict = {}
    for vid in all_videos:
        v_id, c_id, title, desc, views = vid
        words = get_words(f"{title} {desc}")
        video_dict[v_id] = {
            'category_id': c_id,
            'views_count': views,
            'word_vec': Counter(words)
        }

    # Se usuário não tem ID ou não existe, retorna trending (mais vistos)
    if not user_id or user_id == '0':
        trending = sorted(all_videos, key=lambda x: x[4], reverse=True)[:24]
        print(json.dumps([v[0] for v in trending]))
        return

    # Buscar histórico do usuário
    cursor.execute("SELECT video_id, watched_seconds FROM watch_history WHERE user_id = ?", (user_id,))
    history = cursor.fetchall()
    
    if not history:
        # Se não tem histórico, retorna os mais vistos
        trending = sorted(all_videos, key=lambda x: x[4], reverse=True)[:24]
        print(json.dumps([v[0] for v in trending]))
        return

    watched_vids = set([h[0] for h in history])
    
    # Construir perfil do usuário (combinar vetores dos vídeos assistidos)
    user_profile_vec = Counter()
    category_counts = Counter()
    
    for h in history:
        v_id = h[0]
        if v_id in video_dict:
            user_profile_vec.update(video_dict[v_id]['word_vec'])
            category_counts[video_dict[v_id]['category_id']] += 1

    # Pontuar cada vídeo do banco
    scores = []
    for v_id, data in video_dict.items():
        if v_id in watched_vids:
            continue # Não recomenda o que já viu (ou pode recomendar com penalidade)
            
        similarity = cosine_similarity(user_profile_vec, data['word_vec'])
        
        # Bônus de categoria (se for uma categoria que ele gosta, ganha um pequeno boost)
        cat_bonus = 0.05 if data['category_id'] in category_counts else 0.0
        
        # Bônus de popularidade logarítmica para não dominar a similaridade
        pop_bonus = (math.log(data['views_count'] + 1) / 100)
        
        final_score = similarity + cat_bonus + pop_bonus
        
        scores.append((v_id, final_score))

    # Ordenar por pontuação
    scores = sorted(scores, key=lambda x: x[1], reverse=True)
    
    # Retornar os Top 24
    top_recommendations = [s[0] for s in scores[:24]]
    
    # Se não houver recomendações suficientes (banco pequeno), preenche com mais vistos
    if len(top_recommendations) < 24:
        trending = sorted(all_videos, key=lambda x: x[4], reverse=True)
        for t in trending:
            if t[0] not in top_recommendations and t[0] not in watched_vids:
                top_recommendations.append(t[0])
            if len(top_recommendations) >= 24:
                break

    print(json.dumps(top_recommendations))

if __name__ == "__main__":
    main()
