package com.example.mindcare.ui.components

import androidx.compose.animation.animateColorAsState
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.example.mindcare.model.Mood

@Composable
fun MoodSelector(
    selected: Mood?,
    onMoodSelected: (Mood) -> Unit
) {
    Row(
        modifier = Modifier
            .padding(horizontal = 16.dp)
            .height(72.dp),
        horizontalArrangement = Arrangement.spacedBy(12.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Mood.values().forEach { mood ->
            val isSelected = mood == selected
            val containerColor by animateColorAsState(
                targetValue = if (isSelected) {
                    MaterialTheme.colorScheme.primary
                } else {
                    MaterialTheme.colorScheme.surface
                },
                label = "moodColor"
            )
            val contentColor by animateColorAsState(
                targetValue = if (isSelected) {
                    MaterialTheme.colorScheme.onPrimary
                } else {
                    MaterialTheme.colorScheme.onSurface
                },
                label = "moodContentColor"
            )
            Card(
                modifier = Modifier
                    .weight(1f)
                    .clickable { onMoodSelected(mood) },
                colors = CardDefaults.cardColors(containerColor = containerColor),
                elevation = CardDefaults.cardElevation(defaultElevation = if (isSelected) 6.dp else 1.dp),
                shape = RoundedCornerShape(18.dp)
            ) {
                Row(
                    modifier = Modifier
                        .padding(horizontal = 10.dp, vertical = 8.dp),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.Center
                ) {
                    Text(text = mood.emoji, color = contentColor)
                    Spacer(modifier = Modifier.padding(horizontal = 4.dp))
                    Text(
                        text = mood.label,
                        style = MaterialTheme.typography.labelLarge,
                        color = contentColor
                    )
                }
            }
        }
    }
}


